<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Config_page {

    private $plugins;
    private $path_view_project;

    public function __construct() {
        $this->path_view_project = 'application/' . APP_PATH . 'views/project/';
    }

    /*
     * Método com lista de inputs disponíveis
     */

    public function inputs() {
        $input = array();
        $input[] = ['name' => 'Text', 'value' => 'text'];
        $input[] = ['name' => 'Textarea', 'value' => 'textarea'];
        $input[] = ['name' => 'File', 'value' => 'file'];
        $input[] = ['name' => 'Multifile', 'value' => 'multifile'];
        $input[] = ['name' => 'Password', 'value' => 'password'];
        $input[] = ['name' => 'Checkbox', 'value' => 'checkbox'];
        $input[] = ['name' => 'Select', 'value' => 'select'];
        $input[] = ['name' => 'Radio', 'value' => 'radio'];
        $input[] = ['name' => 'Hidden', 'value' => 'hidden'];
        return $input;
    }

    /*
     * Método com lista de tipos de colunas do banco de dados
     */

    public function types() {
        $input = array();
        $input[] = ['type' => 'integer', 'constraint' => 11];
        $input[] = ['type' => 'char', 'constraint' => 128];
        $input[] = ['type' => 'varchar', 'constraint' => 255];
        $input[] = ['type' => 'mediumText'];
        $input[] = ['type' => 'text'];
        $input[] = ['type' => 'longtext'];
        $input[] = ['type' => 'date', 'constraint' => ''];
        $input[] = ['type' => 'datetime', 'constraint' => ''];
        $input[] = ['type' => 'time', 'constraint' => ''];
        $input[] = ['type' => 'year', 'constraint' => ''];
        $input[] = ['type' => 'float', 'constraint' => ''];
        return $input;
    }

    /*
     * Método pra criar arquivo config xml
     */

    public function create_config_xml($fields) {
        $total = count($fields);
        if ($total) {
            $config = array();
            foreach ($fields as $field) {
                $name = $field['name'];
                $input = $field['input'];
                $list_reg = $field['list_reg'];
                $column = $field['column'];
                $type = $field['type'];
                $limit = $field['limit'];
                $plugin = $field['plugin'];
                $required = $field['required'];
                $unique = $field['unique'];
                $options = $field['options'];
                $label_options = $field['label_options'];
                $trigger_select = $field['trigger_select'];
                $new_field = array();
                $attr = array();
                $attr['@type'] = $input;
                $attr['@type_column'] = $type;
                $attr['@list_registers'] = $list_reg;
                $attr['@required'] = $required;
                $attr['@unique'] = $unique;
                $attr['@limit'] = $limit;
                $attr['@column'] = $column;
                if (!empty($plugin)) {
                    $attr['@plugin'] = $plugin;
                }
                if (!empty($options)) {
                    $attr['@options'] = $options;
                }
                if (!empty($label_options)) {
                    $attr['@label_options'] = $label_options;
                }
                if (!empty($trigger_select)) {
                    $attr['@trigger_select'] = $trigger_select;
                }
                $new_field['input'] = $name;
                $config['page']['form'][] = array_merge($attr, $new_field);
            }
            $xml = arrayToXML($config);
            return $xml;
        } else {
            return false;
        }
    }

    /*
     * Método para carregar o arquivo xml
     */

    public function load_config($project, $page, $section) {
        $dir_project = $project;
        $dir_page = $page;
        $dir_section = $section;
        $path = $this->path_view_project . $dir_project . '/' . $dir_page . '/' . $dir_section . '/config.xml';
        if (is_file($path)) {
            $xml = simplexml_load_file($path, 'SimpleXMLElement');
            return $this->treat_config($xml);
        }
    }

    /*
     * Método para tratar os valores do config xml
     */

    private function treat_config($xml) {
        if ($xml) {
            $fields = $xml->form->input;
            $list = false;
            $config = array();
            if ($fields) {
                foreach ($fields as $field) {
                    $attr = $field->attributes();
                    $label = (string) $field;
                    $type = (string) $attr->type;
                    $column = (string) $attr->column;
                    $type_column = (string) $attr->type_column;
                    $list_registers = (string) $attr->list_registers;
                    $required = (string) $attr->required;
                    $unique = (string) $attr->unique;
                    $limit = (string) $attr->limit;
                    $plugin = (string) $attr->plugin;
                    $options = (string) $attr->options;
                    $label_options = (string) $attr->label_options;
                    $trigger_select = (string) $attr->trigger_select;
                    if ($list_registers == '1') {
                        $config['select_query'][] = $column;
                        $config['list'][] = array(
                            'label' => $label,
                            'type' => $type,
                            'column' => $column,
                            'type_column' => $type_column,
                            'list_registers' => $list_registers,
                            'required' => $required,
                            'options' => $options,
                            'label_options' => $label_options,
                            'plugin' => $plugin,
                            'limit' => $limit
                        );
                    }
                    $config['fields'][] = array(
                        'label' => $label,
                        'type' => $type,
                        'column' => $column,
                        'type_column' => $type_column,
                        'list_registers' => $list_registers,
                        'required' => $required,
                        'unique' => $unique,
                        'plugin' => $plugin,
                        'options' => $options,
                        'label_options' => $label_options,
                        'trigger_select' => $trigger_select,
                        'limit' => $limit
                    );
                }
            } else {
                return false;
            }
            return $config;
        } else {
            return false;
        }
    }

    /*
     * Método para criação do template com todos os campos do config xml
     */

    public function fields_template($fields, $post = null) {
        $CI = & get_instance();
        $this->fields = $fields;
        foreach ($fields as $field) {

            // Lista os campos do formulário
            $this->attr = array();
            $this->field = $field;
            $this->type = strtolower($field['type']);
            $this->column = $field['column'];
            $this->required = $field['required'];
            // Recebe os dados da máscara do formulário
            $this->plugin = $this->get_plugin($field['plugin']);
            // Seta o label do arquivo, se for obrigatóro insere asterísco
            $this->label = ($this->required) ? $field['label'] . '<span>*</span>' : $field['label'];
            $this->value = (!empty($post)) ? $post[$this->column] : '';
            $this->post = $post;

            if ($this->plugin) {
                $plugin_added = $this->add_plugin();
            }
            switch ($this->type) {
                case 'file':
                case 'multifile':
                    $new_field = $this->template_input_file();
                    break;
                case 'textarea':
                    $new_field = $this->template_textarea();
                    break;
                case 'checkbox':
                    $new_field = $this->template_checkbox();
                    break;
                case 'select':
                    $new_field = $this->template_select();
                    break;
                case 'hidden':
                    $new_field = $this->template_input_hidden();
                    break;
                default:
                    $new_field = $this->template_input();
                    break;
            }
            $form[] = $new_field;
        }
        return $form;
    }

    /*
     * Método para alterar valor do input, caso tenha algum método para tratar a saida do valor do banco de dados
     */

    private function add_plugin() {
        $plugin = $this->plugin;
        //Se o campo possuir mascara, seta as chaves que existem no array
        $this->attr = (isset($plugin['attr'])) ? $plugin['attr'] : array();
        $js = (isset($plugin['js_form'])) ? $this->change_path($plugin['js_form'], $plugin) : false;
        $css = (isset($plugin['css_form'])) ? $this->change_path($plugin['css_form'], $plugin) : false;
        $class = ucfirst($plugin['plugin']);
        $class_plugin = getcwd() . '/application/' . APP_PATH . 'plugins_input/' . $plugin['plugin'] . '/' . $class . '.php';
        if ($js) {
            add_js($js);
        }
        if ($css) {
            add_css($css);
        }
        if (is_file($class_plugin)) {
            // Se houver um método de saida
            $CI = & get_instance();
            $CI->load->library('../' . APP_PATH . 'plugins_input/' . $plugin['plugin'] . '/' . $class . '.php');
            if (method_exists($class, 'output')) {
                $class = strtolower($class);
                // Se o método existir, aciona e seta o novo valor
                $this->value = $CI->$class->output($this->value, $this->field, $this->fields);
            }
        }
    }

    private function change_path($path, $plugin) {
        if (is_array($path)) {
            foreach ($path as $p) {
                $new_path[] = APP_PATH . 'plugins_input/' . $plugin['plugin'] . '/' . $p;
            }
            return $new_path;
        } else {
            return APP_PATH . 'plugins_input/' . $plugin['plugin'] . '/' . $path;
        }
    }

    /*
     * Método para criar template do input file
     */

    private function template_input_file() {
        // Se o campo for file ou multifile carrega arquivos css e js na página do formulário
        add_css(array(
            'plugins/fancybox/css/jquery.fancybox.css',
            'plugins/fancybox/css/jquery.fancybox-buttons.css',
            'plugins/dropzone/css/dropzone.css',
            '' . APP_PATH . 'project/css/gallery.css'
        ));
        add_js(array(
            'plugins/dropzone/js/dropzone.js',
            'plugins/fancybox/js/jquery.fancybox.pack.js',
            'plugins/fancybox/js/jquery.fancybox-buttons.js',
            'plugins/embeddedjs/ejs.js',
            '' . APP_PATH . 'posts/js/gallery.js'
        ));
        $new_field = array();
        $new_field['type'] = $this->type;
        $new_field['label'] = $this->label;
        $CI = &get_instance();
        $value = ($CI->input->post($this->column) !== null ? $CI->input->post($this->column) : $this->value);
        $files = json_decode($value);
        $this->attr['data-field'] = $this->column;
        $this->attr['class'] = 'form-control btn-gallery ' . (isset($this->attr['class']) ? $this->attr['class'] : '');
        $this->attr['data-toggle'] = 'modal';
        $this->attr['data-target'] = '#gallery';
        $this->attr['type'] = 'button';
        $new_field['input'] = $this->list_files($files);
        $new_field['input'] .= form_button($this->attr, '<span class="fa fa-cloud"></span> Galeria');

        $attr = array();
        if ($this->type == 'multifile') {
            $attr['multiple'] = "true";
        }
        $attr['id'] = $this->column . '-field';
        $attr['name'] = $this->column;
        $attr['type'] = 'hidden';
        $new_field['input'] .= form_input($attr, set_value($this->column, $this->value, false));
        return $new_field;
    }

    /*
     * Método para criar template do textarea
     */

    private function template_textarea() {
        $new_field = array();
        $new_field['type'] = $this->type;
        $new_field['label'] = $this->label;
        $this->attr['name'] = $this->column;
        $this->attr['class'] = 'form-control ' . (isset($this->attr['class']) ? $this->attr['class'] : '');
        $new_field['input'] = form_textarea($this->attr, set_value($this->column, $this->value, false));
        return $new_field;
    }

    /*
     * Método para criar template do checkbox
     */

    private function template_checkbox() {
        add_js(array(
            '' . APP_PATH . 'posts/js/events-select.js'
        ));
        $new_field = array();
        $new_field['type'] = $this->type;
        $new_field['label'] = $this->label;
        $this->attr['name'] = $this->column . '[]';
        $this->attr['class'] = (isset($this->attr['class']) ? $this->attr['class'] : '');
        $CI = &get_instance();
        // Lista registros para o select
        $CI->load->model('posts_model');
        $table = $this->field['options'];
        $column = $this->field['label_options'];
        $posts = $CI->posts_model->list_posts_checkbox($table, $column);
        $opts_checked = array();
        if (!empty($this->value)) {
            $opts_checked = json_decode($this->value);
        }
        if ($posts) {
            $new_field['input'] = '<div>';
            foreach ($posts as $opts) {
                $label = $opts['label'];
                $value = $opts['value'];
                $checked = false;
                if (is_array($opts_checked)) {
                    $checked = (in_array($value, $opts_checked));
                }
                $new_field['input'] .= '<label class="option-checkbox">';
                $new_field['input'] .= form_checkbox($this->attr, $value, $checked);
                $new_field['input'] .= $label;
                $new_field['input'] .= '</label>';
            }
            $new_field['input'] .= '</div>';
        }

        return $new_field;
    }

    /*
     * Método para criar template do select
     */

    private function template_select() {
        add_js(array(
            '' . APP_PATH . 'posts/js/events-select.js'
        ));
        $new_field = array();
        $new_field['type'] = $this->type;
        $new_field['label'] = $this->label;
        if (isset($this->field['options']) && isset($this->field['label_options'])) {
            $column_trigger = (isset($this->field['trigger_select'])) ? $this->field['trigger_select'] : '';
            $data_trigger = null;
            if ($column_trigger) {
                $field_trigger = search($this->fields, 'column', $column_trigger);
                if (count($field_trigger) > 0) {

                    $field_trigger = $field_trigger[0];
                    $table_trigger = $field_trigger['options'];
                    $label_trigger = $field_trigger['label'];
                    $value_trigger = $this->post[$column_trigger];
                    $this->attr['class'] = (isset($this->attr['class'])) ? $this->attr['class'] : '';
                    $this->attr['class'] .= ' trigger-' . $column_trigger;
                    $data_trigger = array(
                        'table' => $table_trigger,
                        'column' => $column_trigger,
                        'value' => $value_trigger,
                        'label' => $label_trigger
                    );
                } else {
                    $field_trigger = null;
                }
            }
            // Lista as opções do select
            $array_options = $this->set_options($this->field['options'], $this->field['label_options'], $data_trigger);
        } else {
            $array_options = array('' => 'Nenhum opção adicionada.');
        }
        $this->attr['class'] = 'form-control trigger-select ' . (isset($this->attr['class']) ? $this->attr['class'] : '');
        $CI = &get_instance();
        $value = ($CI->input->post($this->column) !== null ? $CI->input->post($this->column) : $this->value);
        $new_field['input'] = form_dropdown($this->column, $array_options, $value, $this->attr);
        return $new_field;
    }

    /*
     * Método para criar template do input hidden
     */

    private function template_input_hidden() {
        $new_field = array();
        $this->attr['name'] = $this->column;
        $new_field['type'] = 'hidden';
        $this->attr['class'] = 'form-control ' . (isset($this->attr['class']) ? $this->attr['class'] : '');
        $new_field['input'] = form_input($this->attr, set_value($this->column, $this->value));
        return $new_field;
    }

    /*
     * Método para criar template do input
     */

    private function template_input() {
        $new_field = array();
        $new_field['type'] = $this->type;
        $new_field['label'] = $this->label;
        $this->attr['name'] = $this->column;
        $this->attr['type'] = $this->type;
        $this->attr['class'] = 'form-control ' . (isset($this->attr['class']) ? $this->attr['class'] : '');
        $new_field['input'] = form_input($this->attr, set_value($this->column, $this->value));
        return $new_field;
    }

    /*
     * Método para listar as opções de um select
     */

    private function set_options($table, $column, $data_trigger = null) {
        $CI = & get_instance();
        if (is_array($data_trigger) && empty($data_trigger['value'])) {
            return array('' => 'Selecione um(a) ' . $data_trigger['label']);
        }
        // Lista registros para o select
        $CI->load->model('posts_model');
        $posts = $CI->posts_model->list_posts_select($table, $column, $data_trigger);

        $options = array();
        if ($posts) {
            // Se for encontrado registros
            $options[''] = 'Selecione';
            foreach ($posts as $post) {
                $id = $post['value'];
                $value = $post['label'];
                // Seta os options
                $options[$id] = $value;
            }
        } else {
            $options[''] = 'Nenhuma opção encontrada.';
        }
        return $options;
    }

    /*
     * Método para montar template da listagem de arquivos
     */

    private function list_files($files, $cols = 2) {
        $files = (array) $files;
        $ctt = '<div class="content-files">';
        if ($files) {
            $path = PATH_UPLOAD;
            foreach ($files as $file) {
                $file_ = $file->file;
                if (!empty($file)) {
                    $ctt .= '<div class="files-list thumbnail"><img src="' . base_url('apps/gallery/image/thumb/' . $file_) . '" class="img-responsive"></div>';
                }
            }
        }
        $ctt .= '</div>';
        return $ctt;
    }

    /*
     * Método para tratar a listagem de registros
     * Param posts - contém os registros do banco de dados
     * Param data - contém os dados dos campos de listagem do config xml
     */

    public function treat_list($posts, $data) {
        $list = array();
        // Lista os registros do banco de dados
        foreach ($posts as $row) {
            foreach ($row as $key => $value) {
                // Busca a coluna nos campos do config xml
                $field = search($data, 'column', $key);
                if (isset($field[0])) {
                    // Se o campo for encontrada, faz diversas filtragens no valor da coluna
                    $type = strtolower($field[0]['type']);
                    if (isset($field[0]['plugin'])) {
                        // Se houver um parametro mask setado
                        $value = $this->plugin_output($field[0]['plugin'], $value, $field[0], $data);
                    }
                    switch ($type) {
                        case 'select':
                        case 'radio':
                            $value = $this->treat_options($value, $field);
                            break;
                        case 'file':
                        case 'multifile':
                            $value = $this->treat_value_json($value, $field);
                            break;
                        default:
                            break;
                    }
                }
                $row[$key] = $value;
            }
            $list[] = $row;
        }
        return $list;
    }

    private function plugin_output($plugin, $value, $field, $fields) {
        $type = $field['type'];
        $plugin = $this->get_plugin($plugin);
        $CI = & get_instance();
        $class = ucfirst($plugin['plugin']);
        $class_plugin = getcwd() . '/application/' . APP_PATH . 'plugins_input/' . $plugin['plugin'] . '/' . $class . '.php';
        if (is_file($class_plugin)) {
            $CI->load->library('../' . APP_PATH . 'plugins_input/' . $plugin['plugin'] . '/' . $class . '.php');
            // Verifica se possui um método de saida
            if (method_exists($class, 'output')) {
                $class = strtolower($class);
                // Se o método de saída existir, aciona
                $value = $CI->$class->output($value, $field, $fields);
            }
        }
        switch ($type) {
            case 'checkbox':
                if(!empty($value)){
                    $table = (isset($field['options'])) ? $field['options'] : '';
                    $column = (isset($field['label_options'])) ? $field['label_options'] : '';
                    $opts = json_decode($value);
                    $opts_checked = $CI->posts_model->list_options_checked($table, $column, $opts);
                    if($opts_checked){
                        foreach($opts_checked as $opt){
                            $val[] = $opt['value'];
                        }
                        $value = implode(', ',$val);
                    }
                }
                break;

            default:
                break;
        }
        return $value;
    }

    private function treat_options($value, $field) {
        if (!empty($value) && $value > 0) {
            // Se o valor da coluna não estiver vazio
            $CI = & get_instance();
            $CI->load->model('posts_model');
            $field = $field[0];
            $table = (isset($field['options'])) ? $field['options'] : '';
            $column = (isset($field['label_options'])) ? $field['label_options'] : '';

            if ($table && $column) {
                // Se tabela e coluna existir, lista o valor selecionado pelo campo
                $val = $CI->posts_model->get_post_selected($table, $column, $value);
                if ($val) {
                    // Se um valor for encontrado, seta value com o valor encontrado
                    $value = $val[$column];
                }
            }
        }
        if ($value == '0') {
            $value = '';
        }
        return $value;
    }

    private function treat_value_json($value, $field) {
        if (!empty($value)) {
            // Se o valor da coluna não estiver vazio
            // Decodifica o json
            $files = json_decode($value);
            // Monta o template com as imagens selecionadas e seta no value
            $value = $this->list_files($files, 1);
        }
        return $value;
    }

    /*
     * Método para listar os plugins para usar nos inputs
     * return Array
     */

    public function list_plugins() {
        $path_apps = getcwd() . '/application/' . APP_PATH . 'plugins_input/';
        $opendir = \opendir($path_apps);
        while (false !== ($plugin = readdir($opendir))) {
            if ($plugin != '.' && $plugin != '..') {
                if (is_dir($path_apps . $plugin)) {
                    if (is_file($path_apps . $plugin . '/plugin.yml')) {
                        $this->set_plugin($path_apps . $plugin . '/plugin.yml', $plugin);
                    }
                }
            }
        }
        closedir($opendir);
        asort($this->plugins);
        return $this->plugins;
    }

    /*
     * Método para buscar o arquivo yml e setar o plugin
     * return Array
     */

    private function set_plugin($path, $plugin) {
        $CI = &get_instance();
        $CI->load->library('spyc');
        $config = $CI->spyc->loadFile($path);
        if (is_array($config)) {
            $config['plugin'] = $plugin;
            if (empty($config['name'])) {
                $config['name'] = $config['plugin'];
            }
            $config['name'] = ucfirst(strtolower($config['name']));
            return $this->plugins[] = $config;
        }
    }

    /*
     * Método para buscar um plugin
     * return String
     */

    public function get_plugin($plugin) {
        if (!empty($plugin)) {
            $plugins = $this->plugins;
            if (empty($plugins)) {
                $plugins = $this->list_plugins();
            }
            if ($plugins) {
                $plugin = search($plugins, 'plugin', $plugin);
                if (isset($plugin[0])) {
                    return $plugin[0];
                }
            }
        }
    }

}