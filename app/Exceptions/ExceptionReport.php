<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Traits\ApiResponse;

class ExceptionReport extends Exception
{
    use ApiResponse;

    /**
     * @var Exception
     */
    public $exception;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var
     */
    protected $report;

    /**
     * ExceptionReport constructor.
     * @param Request $request
     * @param Exception $exception
     */
    function __construct(Request $request, Exception $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * TODO:多语言支持
     *
     * @var array
     */
    public $doReport = [
        AuthenticationException::class => ['未授权', 401],
        ModelNotFoundException::class => ['模型未找到', 404],
        ValidationException::class => [],
        AuthorizationException::class => ['权限禁止', 403],
    ];

    /**
     * @return bool
     */
    public function shouldReturn()
    {
        if (!($this->request->wantsJson() || $this->request->ajax())) {
            return false;
        }

        foreach (array_keys($this->doReport) as $report) {
            if ($this->exception instanceof $report) {
                $this->report = $report;
                return true;
            }
        }

        return false;
    }

    /**
     * @param Exception $e
     * @return static
     */
    public static function make(Exception $e)
    {
        return new static(\request(), $e);
    }

    /**
     * @return mixed
     */
    public function report()
    {
        if ($this->exception instanceof ValidationException) {
            return $this->failed($this->exception->errors());
        }

        $message = $this->doReport[$this->report];

        return $this->failed($message[0], $message[1]);
    }
}
