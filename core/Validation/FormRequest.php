<?php
namespace Qadamchi\Validation;

use Qadamchi\Http\Request;
use Qadamchi\Http\Session;
use Qadamchi\Exceptions\ValidationException;

/**
 * Form Request — controller'da validatsiya (Laravel uslubida).
 *   class StoreUserRequest extends FormRequest {
 *       public function authorize(): bool { return true; }
 *       public function rules(): array { return ['email'=>'required|email|unique:users']; }
 *   }
 *   $data = (new StoreUserRequest)->validate();
 */
abstract class FormRequest
{
    protected Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? Request::instance();
    }

    abstract public function rules(): array;

    public function authorize(): bool { return true; }
    public function messages(): array { return []; }

    public function validate(): array
    {
        if (!$this->authorize()) {
            http_response_code(403);
            throw new \RuntimeException('Bu amalga ruxsat yo\'q (403).');
        }

        $validator = new Validator($this->request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            Session::instance()->flash('_errors', $validator->errors());
            Session::instance()->flash('_old_input', $this->request->except(['password', 'password_confirmation', '_token', '_method']));
            throw new ValidationException($validator->errors());
        }

        return $validator->validated();
    }

    public function validated(): array
    {
        return array_intersect_key($this->request->all(), $this->rules());
    }
}