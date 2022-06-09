<?php
function prepareVariables($page, $action = "", $messages = [])
{
    $params['auth'] = is_auth();
    $params['login'] = get_user();
    switch ($page) {
        case('index'):
            $params['title'] = "Лодки напрокат";
            break;

        case('login'):
            $login = $_POST['login'];
            $pass = $_POST['pass'];
            if (auth($login, $pass)) {
                if (isset($_POST['save'])) { // if checkbox checked then create uniqid
                    updateHash();
                }
                header("Location: " . $_SERVER['HTTP_REFERER']);
                die();
            } else {
                echo("Неверный логин пароль");
            }
            break;

        case('logout'):
            setcookie("hash", "", time() - 1, "/");
            session_regenerate_id();
            session_destroy();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            break;

        case('catalog'):
            $params['title'] = "Каталог";
            $params['products'] = getCatalog();
            break;

        case('oneproduct'):
            $params['product'] = getOneProduct();

            $params['title'] = "Отзывы";
            $params['message'] = $messages[$_GET['status']] ?? "";
            doFeedBackAction($params, $action);
            $params['feedbacks'] = getAllFeedbacks();
            $id_session = session_id();
            $id = (int)$_GET['id'];
            addItemInBasket($id_session, $id, $action);
            addLike($action);
            break;

        case('gallery'):
            $params['title'] = "Галерея";
            $params['message'] = $messages[$_GET['status']] ?? "";
            $params['pictures'] = getPictures();

            if (!empty($_FILES)) {
                upload();
            }
            break;

        case('oneitem'):
            $id = $_GET['id'];
            $params['views'] = isVisit($id);
            $params['picture'] = getOnePicture($id);
            break;

        case('documents'):
            $params['title'] = "Файлы";
            $params['files'] = getFiles();
            break;

        case('news'):
            $params['title'] = "Новости";
            $params['news'] = getNews();
            break;

        case('onenews'):
            $id = $_GET['id'];
            $params['news'] = getOneNews($id);
            break;

        case('about'):
            $params['title'] = "О нас";
            $params['phone'] = 79999999;
            break;

        case('basket'):
            $params['title'] = "Корзина";
            $id = (int)$_GET['id'];
            $id_session = session_id();
            $params['basket'] = getBasket($id_session);
            $params['sum'] = getBasketSum($id_session);
            delItemBasket($id, $action);
            order($id_session, $action, $messages);
            $params['message'] = $messages[$_GET['status']] ?? "";
            break;
    }
    return $params;
}