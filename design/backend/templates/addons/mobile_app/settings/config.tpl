<div id="colors_variables">
    <style>
        {$mobile_app_styles nofilter}
    </style>
<!--colors_variables--></div>

<div id="content_mobile_app_configurator">

    <form action="{""|fn_url}" method="post" name="app_config">
        <input type="hidden" name="setting_id" value="{$setting_id}" />

        <div class="span16 mockup__mockups-container">
            {include file="common/subheader.tpl" title="{__(appearance_title)}"}

            <div class="span4 mockup">
                <div class="mockup__container">
                    <div class="mockup__status-bar drawerBgColor__background">

                        <img src="{$images_dir}/addons/mobile_app/status_bar_example.png">

                    </div>

                    <div class="mockup__navbar drawerBgColor__background">
                        <span class="mockup__navbar-left pad-left">
                            <i class="fa fa-bars fa-lg navBarButtonColor"></i>
                        </span>
                        <span class="mockup__navbar-center navBarTextColor">
                            CS-Cart
                        </span>
                        <span class="mockup__navbar-right pad-right">
                            <i class="fa fa-search fa-lg navBarButtonColor"></i>
                            <i class="fa fa-cart-plus fa-lg navBarButtonColor"></i>
                        </span>
                    </div>

                    <div class="mockup__body body screenBackgroundColor__background" style="min-height: calc(100% - 65px); max-height: calc(100% - 65px);">
                        <div class="mockup__product-preview">
                            <img src="{$images_dir}/addons/mobile_app/product_preview.gif">
                        </div>

                        <div class="mockup__product-describes">
                            <p class="mockup__product-title">Mac OS X Lion: The Missing Manual</p>
                            <p class="mockup__product-price">$34.99</p>
                            <p class="mockup__product-desc">For a company that promised to "put a pause on new features," Apple sure has been busy-there's barely a feature left untouched in Mac OS X 10.6 "Snow Leopard." There's more speed, more polish, more refinement-but still no manual. Fortunately, David Pogue is back, with the humor and expertise that have made this the #1 bestselling Mac book for eight years straight. You get all the answers with jargon-free introductions.
                            </p>
                        </div>

                        <div class="mockup__product-tabs tabs">
                            <ul class="tabs__container grayColor__background">
                                <li class="tabs__el tabs__el-active primaryColor">Features</li>
                                <li class="tabs__el">Share</li>
                            </ul>

                            <div class="tabs__content">
                                <p>There are no features</p>
                            </div>
                        </div>
                    </div>

                    <button class="mockup__product-add-to-cart primaryColor__background primaryColorText">Add to cart ($34.99)</button>
                </div>
            </div>

            <div class="span4 mockup">
                <div class="mockup__container">
                    <div class="mockup__status-bar drawerBgColor__background">

                        <img src="{$images_dir}/addons/mobile_app/status_bar_example.png">

                    </div>

                    <div class="mockup__navbar drawerBgColor__background">
                        <span class="mockup__navbar-left pad-left">
                            <i class="fa fa-bars fa-lg navBarButtonColor"></i>
                        </span>
                        <span class="mockup__navbar-center navBarTextColor">
                            CS-Cart
                        </span>
                        <span class="mockup__navbar-right pad-right">
                            <i class="fa fa-search fa-lg navBarButtonColor"></i>
                            <i class="fa fa-cart-plus fa-lg navBarButtonColor"></i>
                        </span>
                    </div>

                    <div class="mockup__body body screenBackgroundColor__background" style="min-height: calc(100% - 65px); max-height: calc(100% - 65px);">
                        <div class="mockup__product-preview">
                            <img src="{$images_dir}/addons/mobile_app/product_preview.gif">
                        </div>

                        <div class="mockup__product-describes">
                            <p class="mockup__product-title">Mac OS X Lion: The Missing Manual</p>
                            <p class="mockup__product-price">$34.99</p>
                            <p class="mockup__product-desc">For a company that promised to "put a pause on new features," Apple sure has been busy-there's barely a feature left untouched in Mac OS X 10.6 "Snow Leopard." There's more speed, more polish, more refinement-but still no manual. Fortunately, David Pogue is back, with the humor and expertise that have made this the #1 bestselling Mac book for eight years straight. You get all the answers with jargon-free introductions.
                            </p>
                        </div>

                        <div class="mockup__product-tabs tabs">
                            <ul class="tabs__container grayColor__background">
                                <li class="tabs__el tabs__el-active primaryColor">Features</li>
                                <li class="tabs__el">Share</li>
                            </ul>

                            <div class="tabs__content">
                                <p>There are no features</p>
                            </div>
                        </div>
                    </div>

                    <button class="mockup__product-add-to-cart primaryColor__background primaryColorText">Add to cart ($34.99)</button>

                    <div class="mockup__overlay contentOverlayColor__background">
                    </div>

                    <div class="mockup__overlay-content navigator navBarBackgroundColor__background">
                        <div class="mockup__overlay-box logo-box grayColor__background">
                            <img class="navigator__store-logo" src="{$images_dir}/addons/mobile_app/logo.png"/>
                            <p class="navBarTextColor">Login | Registration</p>
                        </div>

                        <div class="mockup__overlay-box links-box navBarTextColor">
                            <p class="links-box__container"><i class="fa fa-home navBarButtonColor"></i><span class="links-box__name">Home</span></p>
                            <p class="links-box__container"><i class="fa fa-cart-plus navBarButtonColor"></i><span class="links-box__name">Корзина</span></p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="span4 mockup">
                <div class="mockup__container">
                    <div class="mockup__status-bar drawerBgColor__background">

                        <img src="{$images_dir}/addons/mobile_app/status_bar_example.png">

                    </div>

                    <div class="mockup__navbar drawerBgColor__background">
                        <span class="mockup__navbar-left pad-left">
                            <i class="fa fa-bars fa-lg navBarButtonColor"></i>
                        </span>
                        <span class="mockup__navbar-center navBarTextColor">
                            CS-Cart
                        </span>
                        <span class="mockup__navbar-right pad-right">
                            <i class="fa fa-search fa-lg navBarButtonColor"></i>
                            <i class="fa fa-cart-plus fa-lg navBarButtonColor"></i>
                        </span>
                    </div>

                    <div class="mockup__body body screenBackgroundColor__background mockup__category" style="min-height: calc(100% - 65px); max-height: calc(100% - 65px);">
                        
                        <h3 class="mockup__main-heading darkColor">Categories</h3>

                        <div class="mockup__category-container">
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Electronics</p>
                            </div>
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Books</p>
                            </div>
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Music</p>
                            </div>
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Music</p>
                            </div>
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Music</p>
                            </div>
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Music</p>
                            </div>
                            <div class="mockup__category-item">
                                <img src="{$images_dir}/addons/mobile_app/no_image.png" class="mockup__category-preview"/>
                                <p class="mockup__category-name lightColor">Music</p>
                            </div>
                        </div>

                        <h4 class="mockup__second-heading darkColor">Main banners</h4>
                        <div class="mockup__carousel-container">
                            <img src="{$images_dir}/addons/mobile_app/king.jpg" class="mockup__carousel-img"/>
                        </div>

                        <h4 class="mockup__second-heading darkColor">Hot deals</h4>
                        <div class="mockup__carousel-container">
                            <div class="mockup__carousel-product">
                                <img src="{$images_dir}/addons/mobile_app/nokia.jpg" class="mockup__carousel-product-preview"/>
                                <p class="mockup__carousel-product-describe">
                                    <span class="mockup__carousel-product-name darkColor">Apple iPad with Retina</span>
                                    <span class="mockup__carousel-product-cost lightColor">$499.00</span>
                                </p>
                            </div>
                        </div>

                        <h4 class="mockup__second-heading darkColor">Sale</h4>
                        <div class="mockup__carousel-container">
                            <div class="mockup__carousel-product">
                                <img src="{$images_dir}/addons/mobile_app/led.jpg" class="mockup__carousel-product-preview"/>
                                <p class="mockup__carousel-product-describe">
                                    <span class="mockup__carousel-product-name darkColor">LED 8800 Series Smart TV</span>
                                    <span class="mockup__carousel-product-cost lightColor">$499.00</span>
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div class="span5">
            {include file="common/subheader.tpl" title="{__(nav_colors)}"}

            {foreach $config_data.app_appearance.colors.colors_navigation as $col_name => $color}
            <div class="control-group">
                <label class="control-label" for="">{__($col_name)}:</label>
                <div class="controls colorpicker">
                    <input type="text" 
                           data-target="{$col_name}" 
                           name="m_settings[app_appearance][colors][colors_navigation][{$col_name}]"
                           id="{$col_name}" 
                           value="{$color}" 
                           class="cm-colorpicker"
                    />
                </div>
            </div>
            {/foreach}
        </div>

        <div class="span5">
            {include file="common/subheader.tpl" title="{__(main_colors)}"}

            {foreach $config_data.app_appearance.colors.colors_main as $col_name => $color}
            <div class="control-group">
                <label class="control-label" for="">{__($col_name)}: </label>
                <div class="controls colorpicker">
                    <input type="text" 
                           data-target="{$col_name}" 
                           name="m_settings[app_appearance][colors][colors_main][{$col_name}]"
                           id="{$col_name}" 
                           value="{$color}" 
                           class="cm-colorpicker"
                    />
                </div>
            </div>
            {/foreach}
        </div> 

        <div class="span5">
            {include file="common/subheader.tpl" title="{__(other_colors)}"}

            {foreach $config_data.app_appearance.colors.colors_other as $col_name => $color}
            <div class="control-group">
                <label class="control-label" for="">{__($col_name)}:</label>
                <div class="controls colorpicker">
                    <input type="text" 
                           data-target="{$col_name}" 
                           name="m_settings[app_appearance][colors][colors_other][{$col_name}]"
                           id="{$col_name}" 
                           value="{$color}" 
                           class="cm-colorpicker"
                    />
                </div>
            </div>
            {/foreach}
        </div> 

        <div class="clearfix"></div>

        <div class="span6">
            {include file="common/subheader.tpl" title="{__(app_params)}"}

            {foreach $config_data.app_settings.utility as $col_name => $param}
            <div class="control-group">
                <label class="control-label" for="">{__($col_name)}:</label>
                <div class="controls">
                    <input type="text" name="m_settings[app_settings][utility][{$col_name}]"
                        value="{$param}"
                        data-target="{$col_name}"
                    />
                </div>
            </div>
            {/foreach}
        </div>

        <div class="span9 mobile-app__images-container">
            {include file="common/subheader.tpl" title="{__(images_params)}"}

            {foreach $image_types as $image_type_data}
                <div class="control-group">
                    <label class="control-label">{__("mobile_app.`$image_type_data.name`")}{if !$image_type_data.no_tooltip}{include file="common/tooltip.tpl" tooltip=__("tt_mobile_app.`$image_type_data.name`")}{/if}</label>
                    <div class="controls">
                        {include file="common/attach_images.tpl" image_name=$image_type_data.name image_object_type=$image_type_data.type image_pair=$app_images[$image_type_data.type] hide_alt=true hide_thumbnails=true no_thumbnail=true}
                    </div>
                </div>
            {/foreach}
        </div>

    </form>
</div>