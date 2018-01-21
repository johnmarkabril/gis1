<!DOCTYPE html>
<html>
	<head>
		<!-- THE CSS FILES -->
		<?php $this->load->view('common/css_files'); ?>
	</head>
	<body>
        <div class="loading">Loading&#8230;</div>
        <div class="wrapper">
            <?php $this->load->view('common/navbar'); ?>
            <div class="main-content main-content-padding">
                <?php
                    switch ($curpage) {
                        case 'Template':
                            echo $content;
                            break;
                    }
                ?>
            </div>
        </div>

		<?php $this->load->view('common/js_files'); ?>
	</body>
</html>