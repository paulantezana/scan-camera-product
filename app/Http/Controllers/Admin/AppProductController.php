<?php

namespace App\Http\Controllers\Admin;

use App\Core\BaseController;
use App\Entity\Models\AppProduct;
use App\Exceptions\ValidationException;
use App\Http\Models\Result;
use App\Http\Request;
use App\Http\Response;

class AppProductController extends BaseController
{
  public function product(Request $request, Response $response)
  {
    return $response->view('admin/product', [
      'title' => 'ADMIN',
    ], 'admin', 'product');
  }

  public function table(Request $request, Response $response)
  {
    $res = new Result();

    $body = $request->getBody();

    $appProductModel = new AppProduct();
    $products = $appProductModel->paginate($body);

    $res->result = $products;
    $res->success = true;

    return $response->json($res);
  }

  public function form(Request $request, Response $response)
  {
    return $response->view('admin/productForm', [
      'title' => 'ADMIN',
    ], 'admin', 'productForm');
  }

  public function create(Request $request, Response $response)
  {
    $res = new Result();
    $body = $request->getBody();

    $validate = $this->validateInput($body);
    if (!$validate->success) {
      throw new ValidationException($validate->message);
    }

    $appProductModel = new AppProduct();
    $categoryId =  $appProductModel->insert([
      'code' => htmlspecialchars($body['code']),
      'description' => htmlspecialchars($body['description']),
    ]);

    $res->result = [
      'id' => $categoryId,
    ];
    $res->success = true;
    $res->message = 'El registro se insertó exitosamente';

    return $response->json($res);
  }

  public function update(Request $request, Response $response)
  {
    $res = new Result();
    $body = $request->getBody();

    $validate = $this->validateInput($body, 'update');
    if (!$validate->success) {
      throw new ValidationException($validate->message);
    }

    $appProductModel = new AppProduct();
    $appProductModel->updateById($body['id'], [
      'description' => htmlspecialchars($body['description'])
    ]);

    $res->result = [
      'id' => $body['id'],
    ];
    $res->success = true;
    $res->message = 'El registro se actualizó exitosamente';

    return $response->json($res);
  }

  public function delete(Request $request, Response $response)
  {
    $res = new Result();
    $body = $request->getBody();

    $appProductModel = new AppProduct();
    $appProductModel->deleteById($body['id']);

    $res->success = true;
    $res->message = 'El registro se eliminó exitosamente';

    return $response->json($res);
  }

  public function validate(Request $request, Response $response)
  {
    return $response->view('admin/validate', [
      'title' => 'ADMIN',
    ], 'admin', 'validate');
  }

  public function verified(Request $request, Response $response)
  {
    $res = new Result();
    $body = $request->getBody();

    $code = htmlspecialchars($body['code'] ?? '');

    if (strlen($code) === 0) {
      throw new ValidationException('El código del producto es requerido.');
    }

    $appProductModel = new AppProduct();
    $product = $appProductModel->getBy('code', $body['code']);

    if ($product === false) {
      throw new ValidationException('Código de producto no encontrado. Intente nuevamente.');
    }

    if ($product['verified'] == 1) {
      throw new ValidationException('El código de producto ya fue verificado.');
    }

    $currentDateTime = date('Y-m-d H:i:s');
    $appProductModel->updateById($product['id'], [
      'verified' => '',
      'verified_date' => $currentDateTime,
    ]);

    $res->message = "El producto fue verificado exitosamente.";
    $res->result = $product;
    $res->success = true;

    return $response->json($res);
  }

  public function validateInput($body, $type = 'create')
  {
    $res = new Result();
    $res->success = true;

    if ($type == 'create' || $type == 'update') {
      if (($body['code'] ?? '') == '') {
        $res->message .= 'Falta ingresar el código | ';
        $res->success = false;
      }
      if (($body['description'] ?? '') == '') {
        $res->message .= 'Falta ingresar la descripción | ';
        $res->success = false;
      }
    }

    if ($type == 'update') {
      if (($body['id'] ?? '') == '') {
        $res->message .= 'Falta ingresar el id | ';
        $res->success = false;
      }
    }

    $res->message = trim(trim($res->message), '|');

    return $res;
  }
}
