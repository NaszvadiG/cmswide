<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Users extends MY_Controller {
    /*
     * Variável pública com o limite de usuários por página
     */

    public $limit = 10;

    public function __construct() {
        parent::__construct();
        $this->load->model_app('users_model');
    }

    /*
     * Método com template de listagem de usuários
     */

    public function index() {

        $search = $this->form_search();
        $users = $search['users'];
        $total_rows = $search['total_rows'];
        $pagination = $this->pagination($total_rows);
        $data = [
            'title' => 'Usuários',
            'user_logged' => $this->data_user,
            'users' => $users,
            'pagination' => $pagination,
            'total' => $total_rows
        ];
        add_js([
            'js/index.js'
        ]);
        $this->load->template_app('index', $data);
    }

    /*
     * Método para pesquisa de listagem de usuários
     */

    private function form_search() {
        $this->form_validation->set_rules('search', 'Pesquisa', 'trim|required');
        $keyword = $this->input->get('search');
        $perPage = $this->input->get('per_page');
        $limit = $this->limit;
        $this->form_validation->run();
        $users = $this->users_model->search($keyword, $limit, $perPage);
        $total_rows = $this->users_model->search_total_rows($keyword);
        return array(
            'users' => $users,
            'total_rows' => $total_rows
        );
    }

    /*
     * Método para gerar template de páginação para listagem de usuários
     */

    private function pagination($total_rows) {
        $this->load->library('pagination');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->limit;
        $config['page_query_string'] = true;
        $config['reuse_query_string'] = true;
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_open'] = '</li>';
        $config['first_url'] = '?per_page=0';

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

    /*
     * Método para criação de template para criar usuário 
     */

    public function create() {
        $permissions = $this->apps->list_apps_permissions();
        $this->form_create($permissions);
        $this->load->library('apps');
        add_js([
            '/plugins/switchery/js/switchery.js',
            'js/form.js'
        ]);
        add_css([
            '/plugins/switchery/css/switchery.css'
        ]);
        $vars = [
            'title' => 'Novo usuário',
            'user_logged' => $this->data_user,
            'name' => null,
            'last_name' => null,
            'email' => null,
            'login' => null,
            'status' => null,
            'root' => null,
            'allow_dev' => null,
            'about' => null,
            'permissions' => $permissions,
            'id_user' => ''
        ];
        $this->load->template_app('form', $vars);
    }

    /*
     * Método para criação de usuário
     */

    private function form_create($permissions) {
        $this->form_validation->set_rules('name', 'Nome', 'trim|required');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required|is_unique[wd_users.email]|valid_email');
        $this->form_validation->set_rules('login', 'Login', 'trim|required|is_unique[wd_users.login]|min_length[3]');
        $this->form_validation->set_rules('password', 'Senha', 'trim|required');

        if ($this->form_validation->run()) {
            $this->load->helper('passwordhash');
            $PasswordHash = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $root = $this->input->post('root');
            $allow_dev = $this->input->post('allow_dev');
            if ($this->data_user['root'] != '1') {
                $root = 0;
                $allow_dev = 0;
            }
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'login' => $this->input->post('login'),
                'password' => $PasswordHash->HashPassword($this->input->post('password')),
                'lastname' => $this->input->post('lastname'),
                'status' => $this->input->post('status'),
                'about' => $this->input->post('about'),
                'root' => $root,
                'allow_dev' => $allow_dev
            ];
            $user = $this->users_model->create($data);
            if ($user) {
                $this->create_permissions($permissions, $user);
            }
            add_history('Criou o usuário ' . $this->input->post('name') . ' ' . $this->input->post('lastname'));
            redirect_app('users');
        }
    }

    private function create_permissions($permissions, $id_user) {
        if ($permissions) {
            $data = array();
            foreach ($permissions as $app) {
                $name = $app['name'];
                $dir_app = $app['app'];
                $is_check = (int) $this->input->post($dir_app);
                $permissions_app = (isset($app['permissions']) ? $app['permissions'] : array());
                $data[] = array(
                    'fk_user' => $id_user,
                    'app' => $dir_app,
                    'page' => '',
                    'method' => '',
                    'status' => $is_check
                );
                foreach ($permissions_app as $page => $arr) {
                    foreach ($arr as $key => $value) {
                        $method = $key;
                        $label = $value;
                        if (!is_array($label)) {
                            $is_check = (int) $this->input->post($dir_app . '-' . $method);
                            if ($page == '/') {
                                $page = '';
                            }
                            $data[] = array(
                                'fk_user' => $id_user,
                                'app' => $dir_app,
                                'page' => $page,
                                'method' => $method,
                                'status' => $is_check
                            );
                        } else {
                            foreach ($value as $method => $label) {
                                $page = $key;
                                $is_check = (int) $this->input->post($dir_app . '-' . $method);
                                $data[] = array(
                                    'fk_user' => $id_user,
                                    'app' => $dir_app,
                                    'page' => $page,
                                    'method' => $method,
                                    'status' => $is_check
                                );
                            }
                        }
                    }
                }
            }
            $this->users_model->create_permissions($data);
        }
    }

    /*
     * Método para criação de template de edição de usuário
     */

    public function edit($login) {
        $user = $this->users_model->get_user_edit($login);
        if (!$user) {
            redirect_app('users');
        }
        $permissions = $this->apps->list_apps_permissions();
        $this->form_edit($user, $permissions);

        add_js([
            'js/form.js'
        ]);
        $vars = [
            'title' => 'Editar usuário',
            'id_user' => $user['id'],
            'name' => $user['name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'login' => $user['login'],
            'status' => $user['status'],
            'root' => $user['root'],
            'allow_dev' => $user['allow_dev'],
            'about' => $user['about'],
            'permissions' => $permissions
        ];
        $this->load->template_app('form', $vars);
    }

    /*
     * Método para edição de usuário
     */

    private function form_edit($user, $permissions) {
        $this->form_validation->set_rules('name', 'Nome', 'trim|required');
        if ($this->input->post('email') != $user['email']) {
            $this->form_validation->set_rules('email', 'E-mail', 'trim|required|is_unique[wd_users.email]|valid_email');
        }
        if ($this->input->post('login') != $user['login']) {
            $this->form_validation->set_rules('login', 'Login', 'trim|required|is_unique[wd_users.login]|min_length[3]');
        }
        if ($this->form_validation->run()) {
            if ($user['root']) {
                $root = $this->input->post('root');
                $allow_dev = $this->input->post('allow_dev');
            } else {
                $root = $user['root'];
                $allow_dev = $user['allow_dev'];
            }
            $data = [
                'login_old' => $user['login'],
                'name' => $this->input->post('name'),
                'lastname' => $this->input->post('lastname'),
                'status' => $this->input->post('status'),
                'email' => $this->input->post('email'),
                'login' => $this->input->post('login'),
                'about' => $this->input->post('about'),
                'root' => $root,
                'allow_dev' => $allow_dev,
            ];
            $password = $this->input->post('password');
            if (!empty($password)) {
                $this->load->helper('passwordhash');
                $PasswordHash = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
                $data['password'] = $PasswordHash->HashPassword($password);
            } else {
                $data['password'] = $user['password'];
            }
            $this->users_model->update($data);
            $this->users_model->delete_permissions($user['id']);
            $this->create_permissions($permissions, $user['id']);
            add_history('Editou o usuário ' . $this->input->post('name'));
            redirect_app('users');
        }
    }

    /*
     * Método para deletar usuário
     */

    public function delete() {
        $del = $this->input->post('del');
        if ($del > 1) {
            $this->users_model->delete($del);
        }
        $user = $this->users_model->get_user($del);
        add_history('Removeu o usuário ' . $user['name']);
        redirect_app('users');
    }

    /*
     * Método para ativar e desativar o modo desenvolvedor, acionado por js
     */

    public function dev_mode() {
        $this->form_validation->set_rules('dev', 'Dev', 'required');
        if ($this->form_validation->run()) {
            $dev = $this->input->post('dev');
            $this->users_model->change_mode(['dev' => $dev, 'id_user' => $this->data_user['id']]);
        }
    }
    
    public function profile($login) {
        $user = $this->users_model->get_user_edit($login);
        if(!$user){
            redirect();
        }
        $offset = (int) $this->input->get('per_page');
        add_css('css/profile.css');
        $history = read_history(array(
            'limit' => 10,
            'offset' => $offset,
            'order_by' => 'id DESC',
            'where' => array(
                'wd_users.login' => $login
            )
        ));
        $vars = array(
            'title' => $user['name'].' '.$user['last_name'],
            'name' => $user['name'],
            'login' => $user['login'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'image' => $user['image'],
            'about' => $user['about'],
            'history' => $history,
            'total_history' => $history['total'],
            'pagination' => $this->pagination_history($history['total'])
        );
        $this->load->template_app('profile', $vars);
    }
    
    private function pagination_history($total_rows) {
        $this->load->library('pagination');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['page_query_string'] = true;
        $config['reuse_query_string'] = true;
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_open'] = '</li>';
        $config['first_url'] = '?per_page=0';

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

}
