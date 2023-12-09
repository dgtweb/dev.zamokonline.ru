<?php

/**
 * @global CMain $APPLICATION
 **/

?>

<?php \Uisoft\App\IncludeHtmlTemplate::BottomPageSection(); ?>
</main>

<footer class="footer-wrapper">
    <div class="footer-main-wrapper">
        <div class="container">
            <div class="row">
                <div class="footer-contact-container col-6 col-md-4 col-lg-auto">
                    <div class="footer-group">
                        <h5 class="footer-title">Контакты</h5>

                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW"   => "file",
                                "AREA_FILE_SUFFIX" => "inc",
                                "EDIT_TEMPLATE"    => "",
                                "PATH"             => "/include/bottom/contacts.inc.php"
                            )
                        ); ?>
                    </div>
                </div>

                <div class="footer-navigation-container col-6 col-md-4 col-lg-auto">
                    <div class="footer-group">
                        <h5 class="footer-title">О компании</h5>

                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW"   => "file",
                                "AREA_FILE_SUFFIX" => "inc",
                                "EDIT_TEMPLATE"    => "",
                                "PATH"             => "/include/bottom/menu_company.inc.php"
                            )
                        ); ?>
                    </div>
                </div>

                <div class="footer-navigation-container col-6 col-md-4 col-lg-auto">
                    <div class="footer-group">
                        <h5 class="footer-title">Покупателям</h5>

                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW"   => "file",
                                "AREA_FILE_SUFFIX" => "inc",
                                "EDIT_TEMPLATE"    => "",
                                "PATH"             => "/include/bottom/menu_to_buyers.inc.php"
                            )
                        ); ?>
                    </div>
                </div>

                <div class="footer-navigation-container col-6 col-md-4 col-lg-auto">
                    <div class="footer-group">
                        <h5 class="footer-title">Услуги</h5>

                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW"   => "file",
                                "AREA_FILE_SUFFIX" => "inc",
                                "EDIT_TEMPLATE"    => "",
                                "PATH"             => "/include/bottom/menu_services.inc.php"
                            )
                        ); ?>
                    </div>
                </div>

                <div class="footer-navigation-container col-6 col-md-4 col-lg-auto">
                    <div class="footer-group">
                        <h5 class="footer-title">Информация</h5>
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW"   => "file",
                                "AREA_FILE_SUFFIX" => "inc",
                                "EDIT_TEMPLATE"    => "",
                                "PATH"             => "/include/bottom/menu_info.inc.php"
                            )
                        ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-copyright-wrapper">
        <div class="container">
            <? $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW"   => "file",
                    "AREA_FILE_SUFFIX" => "inc",
                    "EDIT_TEMPLATE"    => "",
                    "PATH"             => "/include/bottom/copyright.inc.php"
                )
            ); ?>
        </div>
    </div>
</footer>

</body>
</html>