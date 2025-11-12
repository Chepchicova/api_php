<?php
class ServiceController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function handleRequest($method, $id = null, $input = null) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $data = $this->model->getById($id);
                    if ($data) {
                        echo json_encode($data);
                    } else {
                        http_response_code(404);
                        echo json_encode(["error" => "Not found"]);
                    }
                } else {
                    echo json_encode($this->model->getAll());
                }
                break;

            case 'POST':
                // Валидация входных данных
                $errors = [];

                if (empty($input['customer_id']) || !is_numeric($input['customer_id'])) {
                    $errors[] = "Поле customer_id обязательно и должно быть числом";
                }

                if (empty($input['issue_description']) || strlen(trim($input['issue_description'])) < 5) {
                    $errors[] = "Поле описания проблемы обязательно и должно содержать не менее 5 символов";
                }

                if (empty($input['status']) || !in_array($input['status'], ['open', 'in_progress', 'closed'])) {
                    $errors[] = "Поле status обязательно и должно быть одним из: open, in_progress, closed";
                }

                if (!empty($errors)) {
                    http_response_code(400); // Unprocessable Entity
                    echo json_encode(["validation_errors" => $errors]);
                    break;
                }

                // Если ошибок нет — создаём запись
                if ($this->model->create($input)) {
                    http_response_code(201);
                    echo json_encode(["message" => "Created"]);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Failed to create"]);
                }
                break;

case 'PUT':
    // Проверка: есть ли тело запроса
    if (empty($input) || !is_array($input)) {
        http_response_code(400);
        echo json_encode(["error" => "Пустое или некорректное тело запроса"]);
        break;
    }

    // Валидация входных данных
    $errors = [];

    if (isset($input['customer_id']) && !is_numeric($input['customer_id'])) {
        $errors[] = "Поле customer_id должно быть числом";
    }

    if (isset($input['issue_description']) && strlen(trim($input['issue_description'])) < 5) {
        $errors[] = "Поле issue_description должно содержать не менее 5 символов";
    }

    if (isset($input['status']) && !in_array($input['status'], ['open', 'in_progress', 'closed'])) {
        $errors[] = "Поле status должно быть одним из: open, in_progress, closed";
    }

    if (!empty($errors)) {
        http_response_code(422); // Unprocessable Entity
        echo json_encode(["validation_errors" => $errors]);
        break;
    }

    // Если ошибок нет — обновляем запись
    if ($id && $this->model->update($id, $input)) {
        echo json_encode(["message" => "Updated"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Failed to update"]);
    }
    break;


            case 'DELETE':
                if ($id && $this->model->delete($id)) {
                    echo json_encode(["message" => "Deleted"]);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Failed to delete"]);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"]);
        }
    }
}
