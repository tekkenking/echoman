<?php

declare(strict_types=1);

namespace Tekkenking\Echoman;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class Echoman
{
    private array $data;

    private int $statusCode;

    /**
     * @param string $status
     * @return $this
     */
    public function status(string $status): self
    {
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @throws EchomanException
     */
    public function validationError(Validator $validator, $msg='validation failed', bool $throwException = false): Echoman
    {
        $this->data['messages'] = $validator->errors()->all();
        $res = $this->status('error')
            ->messages($msg)
            ->data($validator->errors());

        if($throwException) {
            throw new EchomanException($this->get());
        }else{
            return $res;
        }

    }

    /**
     * @throws EchomanException
     */
    public function throwValidationError(Validator $validator, $msg='validation failed'): void
    {
        $this->validationError($validator, $msg, true);
    }

    /**
     * @throws EchomanException
     */
    public function throwError(string $msg, int $code=400): void
    {
        $this->status('error')
            ->message($msg);
        throw new EchomanException($this->get($code));
    }

    /**
     * @param string $msg
     * @param int $code
     * @return void
     * @throws EchomanException
     */
    public function exception(string $msg, int $code=400): void
    {
        $this->throwError($msg, $code);
    }

    /**
     * @param string $msg
     * @param int $code
     * @return void
     * @throws EchomanException
     */
    public function throwOops(string $msg ="Oops! something went wrong", int $code = 500): void
    {
        $this->throwError($msg, $code);
    }


    /**
     * @param string $msg
     * @return Echoman
     */
    public function message(string $msg): Echoman
    {
        if($msg) {
            $this->data['message'] = $msg;
        }
        return $this;
    }

    /**
     * @param array|string $msg
     * @return Echoman
     */
    public function messages(array | string $msg): Echoman
    {
        if($msg) {
            if(is_array($msg)) {
                $this->data['messages'] = $msg;
            }else{
                $this->data['message'] = $msg;
            }
        }
        return $this;
    }

    /**
     * @param $data
     * @return Echoman
     */
    public function data($data): Echoman
    {
        if($data) {
            $this->data['data'] = $data;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param string $name
     * @param array $data
     * @return $this
     */
    public function view(string $name, array $data = []): Echoman
    {
        if($name) {
            $this->data['view'] = $name;
            $this->data['view_data'] = $data;
        }
        return $this;
    }

    /**
     * @param mixed $data
     * @param int $code
     * @throws EchomanException
     */
    public function dd(mixed $data, int $code = 400)
    {
        $this->data['message'] = 'dump die';
        $this->data['data'] = $data;
        throw new EchomanException($this->get($code));
    }

    /**
     * @param int $code
     * @return Factory|Response|JsonResponse|View|Application|ResponseFactory|string
     * @throws EchomanException
     */
    public function get(int $code=200): Factory|Response|JsonResponse|View|Application|ResponseFactory|string
    {
        $this->statusCode = $code;

        if(Request::header('Call-type') && Request::header('Call-type') === 'api-call'){
            return response()->json($this->data, $this->statusCode);
        }

        $this->data['status'] = $this->data['status'] ?? 'success';

        if(Request::ajax() && isset($this->data['view']) && !empty($this->data['view'])) {
            return $this->makeViewBlade(true);
        }

        if(Request::ajax()) {
            return response()->json($this->data, $this->statusCode);
        }

        if(isset($this->data['view']) && !empty($this->data['view'])){
            //View is needed
            return $this->makeViewBlade();
        }

        $this->data['message'] = 'Unknown return type';
        throw new EchomanException('Exception: Could not determine if it\'s json or blade response');
        //return response($this->data, $this->statusCode);
    }

    /**
     * @param bool $isRender
     * @return Application|Factory|\Illuminate\Contracts\View\View|string
     */
    private function makeViewBlade(bool $isRender = false): \Illuminate\Contracts\View\View|Factory|string|Application
    {
        $viewFile = $this->data['view'];

        if(isset($this->data['view_data']) && !empty($this->data['view_data'])) {
            //A separate view data is passed
            $viewData = $this->data['view_data'];
        }

        elseif(isset($this->data['data'])) {
            $viewData = $this->data['data'];
        }

        else {
            $viewData = [];
        }

        if($isRender) {
            return view($viewFile, $viewData)->render();
        }

        //return theme_view($viewFile, $viewData);
        return view($viewFile, $viewData);
    }
}
