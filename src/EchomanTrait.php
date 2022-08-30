<?php

declare(strict_types=1);

namespace Tekkenking\Echoman;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

trait EchomanTrait
{
    /**
     * @return Echoman
     */
    public function sendEcho(): Echoman
    {
        return new Echoman();
    }

    /**
     * @param array|string $msg
     * @param array $data
     * @return JsonResponse
     */
    public function sendResponse(array | string $msg, array $data = []): JsonResponse
    {
        return $this->sendEcho()
            ->messages($msg)
            ->data($data)
            ->get();
    }

    /**
     * @param array|string $msg
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(array | string $msg, array $data = [], int $code = 400): JsonResponse
    {
        return $this->sendEcho()
            ->status('error')
            ->messages($msg)
            ->data($data)
            ->get($code);
    }

    /**
     * @param string $msg
     * @param int $code
     * @return void
     * @throws EchomanException
     */
    public function sendEx(string $msg, int $code = 400): void
    {
        $this->sendEcho()->throwError($msg, $code);
    }

    /**
     * @param int $code
     * @return void
     * @throws EchomanException
     */
    public function sendOops(int $code = 500): void
    {
        $this->sendEcho()->oops(null, $code);
    }

    /**
     * @param Validator $validator
     * @param int $statuscode
     * @param bool $exception
     * @return JsonResponse
     * @throws EchomanException
     */
    public function sendValidationError(
        Validator $validator,
        int $statuscode = 200,
        bool $exception = false): JsonResponse
    {
        return $this->sendEcho()
            ->throwValidationError($validator, 'validation failed', $exception)
            ->get($statuscode);
    }

}
