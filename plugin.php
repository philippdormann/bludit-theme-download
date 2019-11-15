<?php
class themeDownload extends Plugin
{
    public function init()
    {
        $this->formButtons = false;
    }
    
    public function post()
    {
        if (isset($_POST['install'])) {
            $error = 0;
            $f = file_put_contents("theme_to_install.zip", fopen($_POST['install'], 'r'), LOCK_EX);//download theme zip
            if (!$f) {
                $error = -1;
            }
            $zip = new ZipArchive;
            $res = $zip->open("theme_to_install.zip");
            if ($res) {
                $zip->extractTo(PATH_THEMES);
                $zip->close();
            } else {
                $error = -2;
            }
            switch ($error) {
                case 0:
                break;
                case -1:
                echo 'Could not download file (error code: -1)<br><a href="'.HTML_PATH_ADMIN_ROOT.'">go back to admin page</a>';
                exit;
                break;
                case -2:
                echo 'Could not open zip archive (error code: -2)<br><a href="'.HTML_PATH_ADMIN_ROOT.'">go back to admin page</a>';
                break;
                default:
                echo 'An unexpected error happend (error code: -9)<br><a href="'.HTML_PATH_ADMIN_ROOT.'">go back to admin page</a>';
                break;
            }
            unlink("theme_to_install.zip");
        }
    }
    
    public function form()
    {
        global $L;
        $html  = '<div class="alert alert-primary" role="alert">'.$this->description().'</div>
        <div class="alert alert-primary" role="alert"><strong>Info: </strong> This plugin requires JS to be enabled</div>
        <input type="text" class="light-table-filter" data-table="order-table" placeholder="Search for anything..">
        <script>"use strict"; var LightTableFilter=function (Arr){var filterInput; function _onInputEvent(e){filterInput=e.target; var tables=document.getElementsByClassName(filterInput.getAttribute("data-table")); Arr.forEach.call(tables, function (table){Arr.forEach.call(table.tBodies, function (tbody){Arr.forEach.call(tbody.rows, _filter);});});}function _filter(row){var text=row.textContent.toLowerCase(), val=filterInput.value.toLowerCase(); row.style.display=text.indexOf(val)===-1 ? "none" : "table-row";}return{init: function init(){var inputs=document.getElementsByClassName("light-table-filter"); Arr.forEach.call(inputs, function (input){input.oninput=_onInputEvent;});}};}(Array.prototype); document.addEventListener("readystatechange", function (){if (document.readyState==="complete"){LightTableFilter.init();}}); </script>
        <table id="theme-download-extension-table" class="table mt-3 order-table"><thead><tr>
        <th class="border-bottom-0 w-25" scope="col">Name</th>
        <th class="border-bottom-0 d-none d-sm-table-cell" scope="col">Description</th>
        <th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col">Version</th>
        <th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col">Author</th>
        </tr></thead><tbody id="theme-download-extension-table-body"></tbody></table>';
        
        return $html;
    }
    public function adminSidebar()
    {
        return '<li class="nav-item"><a class="nav-link" href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/themeDownload">Theme Download</a></li>';
    }
    public function adminBodyEnd()
    {
        $scripts  = '<script>
        $(function() {
            $.get("https://api.github.com/repos/bludit/themes-repository/contents/items", function(data) {
                for (var i = 0; i < data.length; i++) {
                    $.get("https://raw.githubusercontent.com/bludit/themes-repository/master/items/"+data[i].name+"/metadata.json", function(data) {
                        var data = JSON.parse(data);
                        var theme_name = data.name;
                        var theme_version = data.version;
                        var theme_download = data.download_url;
                        if(data.download_url_v2 != undefined){
                            theme_download = data.download_url_v2;
                        }
                        var theme_information_url = data.information_url;
                        var theme_description = data.description;
                        var theme_author_username = data.author_username;
                        
                        var new_table_row = `<tr>
                        <td class="align-middle pt-3 pb-3"><div>`+theme_name+`</div><div class="mt-1"><button name="install" class="btn btn-primary my-2" type="submit" value="`+theme_download+`">Install</button></div></td>
                        <td class="align-middle d-none d-sm-table-cell"><div>`+theme_description+`</div><a href="`+theme_information_url+`" target="_blank">More information</a></td>
                        <td class="text-center align-middle d-none d-lg-table-cell"><span>`+theme_version+`</span></td>
                        <td class="text-center align-middle d-none d-lg-table-cell"><a target="_blank">`+theme_author_username+`</a></td>
                        </tr>`;
                        
                        $("#theme-download-extension-table-body").append(new_table_row);
                    });
                }
            });
        });
        </script>';
        return $scripts;
    }
}
