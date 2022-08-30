<?php

declare(strict_types=1);

namespace Tekkenking\Echoman;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

class Echoman
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @param string $status
     * @return $this
     */
    public function status(string $status): Echoman
    {
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @param string $type
     * @param string $msg
     * @param int $status
     * @param array $data
     * @return JsonResponse
     */
    private function commonDefaults(string $type, string $msg, int $status, array $data): JsonResponse
    {
        return $this->status($type)
            ->message($msg)
            ->data($data)
            ->get($status);
    }

    /**
     * @param string $msg
     * @param int $status
     * @param array $data
     * @return JsonResponse
     */
    public function error(string $msg, int $status = 200, array $data = []): JsonResponse
    {
        return $this->commonDefaults('error', $msg, $status, $data);
    }

    /**
     * @param string $msg
     * @param int $status
     * @param array $data
     * @return JsonResponse
     */
    public function success(string $msg, int $status = 200, array $data = []): JsonResponse
    {
        return $this->commonDefaults('success', $msg, $status, $data);
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
     * @param Validator $validator
     * @param string $msg
     * @param bool $exception
     * @return Echoman
     * @throws EchomanException
     */
    public function throwValidationError(
        Validator $validator,
        string $msg='validation failed',
        bool $exception = true): Echoman
    {
        return $this->validationError($validator, $msg, $exception);
    }

    /**
     * @throws EchomanException
     */
    public function throwError(string $msg, int $code=400)
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
     * @param string|null $msg
     * @param int $code
     * @return void
     * @throws EchomanException
     */
    public function oops(string | null $msg = null, int $code = 500): void
    {
        $this->exception( $msg ?? 'Oops! something went wrong', $code);
    }

    /**
     * @param string $msg
     * @return $this
     */
    public function message(string $msg): self
    {
        if($msg) {
            $this->data['message'] = $msg;
        }
        return $this;
    }

    /**
     * @param array|string $msg
     * @return $this
     */
    public function messages(array | string $msg): self
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
     * @return $this
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

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $code
     * @return JsonResponse
     */
    public function get(int $code=200): JsonResponse
    {
        $this->statusCode = $code;
        $this->data['status'] = $this->data['status'] ?? 'success';
        return response()->json($this->data, $this->statusCode);
    }
}
