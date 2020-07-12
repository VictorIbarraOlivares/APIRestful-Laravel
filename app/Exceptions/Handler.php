<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ( $exception instanceof ValidationException ) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ( $exception instanceof ModelNotFoundException ) {
            $modelo = strtolower( \class_basename( $exception->getModel() ) );
            return $this->errorResponse("No existe instancia de {$modelo} con el id expecificado", 404);
        }

        if ( $exception instanceof AuthenticationException ) {
            return $this->unauthenticated($request, $exception);
        }

        if ( $exception instanceof AuthorizationException ) {
            return $this->errorResponse('No tiene psermisos para ejecutar esta acción', 403);
        }

        if ( $exception instanceof NotFoundHttpException ) {
            return $this->errorResponse('No se encontró la URL especificada', 404);
        }

        if ( $exception instanceof MethodNotAllowedHttpException ) {
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }

        if ( $exception instanceof HttpException ) {
            return $this->errorResponse( $exception->getMessage(), $exception->getStatusCode() );
        }

        if ( $exception instanceof QueryException ) {

            $codigo = $exception->errorInfo[1];
            if ( $exception->errorInfo[1] == 1451 ) {
                return $this->errorResponse(
                    'No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro', 409
                );
            }
            
        }

        if ( config('app.debug') ) {
            return parent::render($request, $exception);
        } else {
            return $this->errorResponse('Falla inesperada. Intente luego', 500);
        }
        
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
        // if ($e->response) {
        //     return $e->response;
        // }

        // return $request->expectsJson()
        //             ? $this->invalidJson($request, $e)
        //             : $this->invalid($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('No autenticado.', 401);
        // return $request->expectsJson()
        //             ? response()->json(['message' => $exception->getMessage()], 401)
        //             : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
    
}
