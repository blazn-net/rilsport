<?php
namespace Module\Main\Controller;

use Core\Controller;

class Lang extends Controller {
    private $langModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('main'));
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'USER_ERR_UNAUTHORIZED');
        }

        $this->langModel = $this->model('main/Lang');
    }

    public function index($id = null) {
        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('main'));
        $langData = null;

        if ($id) {
            $langData = $this->langModel->getLangByCode($id);
            if (!$langData) {
                return $this->redirect('main/langs');
            }
        }

        $data = [
            'txt' => $txt,
            'title' => ($id ? 'Modifier la langue' : 'Ajouter une langue') . ' - ' . SITENAME,
            'lang' => $langData ? (object) $langData : null,
            'mode' => $id ? 'edit' : 'add',
            'error' => $_SESSION['flash_error'] ?? '',
            'message' => $_SESSION['flash_message'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = [
                'lang_code' => $id ? $id : trim($_POST['lang_code'] ?? ''),
                'lang_name' => trim($_POST['lang_name'] ?? ''),
                'lang_flag' => trim($_POST['lang_flag'] ?? ''),
                'status_id' => isset($_POST['status_id']) ? (int) $_POST['status_id'] : ($id ? $langData['status_id'] : 1),
                'user_id'   => $_SESSION['user_id'] ?? 1
            ];

            if (empty($postData['lang_code']) || empty($postData['lang_name'])) {
                $data['error'] = $txt['ERR_MISSING_LANG_FIELDS'] ?? 'ERR_MISSING_LANG_FIELDS';
                $data['lang'] = (object) $_POST;
            } else {
                try {
                    if ($id) {
                        // Modification (keeping the original code in the array)
                        $postData['lang_code'] = $id; 
                        $this->langModel->updateLang($postData);

                        $_SESSION['flash_message'] = $txt['MSG_LANG_UPDATED'] ?? 'MSG_LANG_UPDATED';
                        return $this->redirect('main/lang/' . urlencode($id));
                    } else {
                        // Ajout
                        $this->langModel->addLang($postData);

                        $_SESSION['flash_message'] = $txt['MSG_LANG_ADDED'] ?? 'MSG_LANG_ADDED';
                        return $this->redirect('main/langs');
                    }
                } catch (\Exception $e) {
                    $data['error'] = ($txt['USER_ERR_UPDATE_FAIL'] ?? 'ERR_UPDATE_FAIL : ') . $e->getMessage();
                    $data['lang'] = (object) $_POST;
                }
            }
        }

        $this->view('system/header', $data);
        $this->view('system/sidebar', $data);
        $this->view('main/lang', $data);
        $this->view('system/footer', $data);
    }

    public function delete($lang_code) {
        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('main'));
        if (!empty($lang_code)) {
            try {
                $user_id = $_SESSION['user_id'] ?? 1;
                $this->langModel->softDeleteLang($lang_code, $user_id);
                $_SESSION['flash_message'] = $txt['SUCCESS_DEACTIVATE_LANG'] ?? 'SUCCESS_DEACTIVATE_LANG';
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['ERR_DEACTIVATE_LANG'] ?? 'ERR_DEACTIVATE_LANG : ') . $e->getMessage();
            }
        }
        return $this->redirect('main/langs');
    }

    public function forcedelete($lang_code) {
        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('main'));
        if (!empty($lang_code)) {
            try {
                $this->langModel->forceDeleteLang($lang_code);
                $_SESSION['flash_message'] = $txt['SUCCESS_DELETE_LANG'] ?? 'SUCCESS_DELETE_LANG';
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['ERR_DELETE_LANG'] ?? 'ERR_DELETE_LANG : ') . $e->getMessage();
            }
        }
        return $this->redirect('main/langs');
    }
}
