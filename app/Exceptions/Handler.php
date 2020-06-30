<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        // parent::render => going to show details that we dont realy want to share it with the client or the user so we should check of this error exception type

        if ($exception instanceof ValidationException) {
            // cal this function to send respective exeption
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        // when we obtain an a specific instance of model does'nt exist
        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exist any '{$modelName}' with the specified identificator", 404);
        }

        // when some user try to access to specific action without any authunticated to the system
        if ($exception instanceof AuthenticationException) {
            // cal this function to send respective exeption
            return $this->unauthenticated($request, $exception);
        }

        // when some user is authunticated to the system but dont have permissions for specific action
        if ($exception instanceof AuthorizationException) {
            // cal this function to send respective exeption
            return $this->errorResponse($exception->getMessage(), 403);
        }

        // when some user try to access a resource does'nt exist, like wrong URL
        if ($exception instanceof NotFoundHttpException) {
            // cal this function to send respective exeption
            return $this->errorResponse("The specified URL not found", 404);
        }

        // when some user try to send a request to a specific real route but with wrong http method
        if ($exception instanceof MethodNotAllowedHttpException) {
            // cal this function to send respective exeption
            return $this->errorResponse("The specified method for the request is ivalid", 405);
        }

        // general role to handle any other kind of http exceptions
        if ($exception instanceof HttpException) {
            // cal this function to send respective exeption
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        // handle exception is not related with http => to handle exception related to the db, remove resource related to other resource => this operation cannot be possible because we are doing any violation of the FK constraints
        if ($exception instanceof QueryException) {
            // dd($exception);
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1451) {
                return $this->errorResponse('Cannot remove this resource permently, Its related with other resource', 409);
            }
        }

        // handle exception if token not valid for csrf security
        if ($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }

        // handle an expected exception like call fail in any moment
        // any exception does'nt match with any of above conditions
        // if we are codding/debugging we want to show the error for the developer
        if (Config('app.debug')) {
            return parent::render($request, $exception);
        }

        // handle an expected exception like call fail in any moment
        // any exception does'nt match with any of above conditions
        // 500 => means the exception from the server side only
        return $this->errorResponse('Unexpected Excption, Try later', 500);
    }

    // overridding the method from parent class from render method
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        // here we send request from web but as ajax request so we should return special error response
        if ($this->isFrontEnd($request)) {
            return $request->ajax() ? response()->json($errors, 422) : redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }
        return $this->errorResponse($errors, 422);
    }

    // handle the unauthuticaton request
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // check request comming from
        if ($this->isFrontEnd($request)) {
            return redirect()->guest('login');
        }
        return $this->errorResponse('Unauthenticated', 401);
    }

    // check if the request comming from web or API
    private function isFrontEnd($request)
    {
        // if the request has accept HTML and contain web middleware ===> its web request
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
