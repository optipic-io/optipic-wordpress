<div class="wrap" id="optipic-settings-form-wrap">
    <h2><?_e("OptiPic Plugin 0ptions", "optipic")?></h2>
    <form method="post"  action="options.php">
        <?php settings_fields('op-settings-group');?>
        <?php 
        $optipic_options = get_option('optipic_options');
        
        $siteId = !empty($optipic_options['cdn_site_id'])? $optipic_options['cdn_site_id']: ''; 
        $exclusionsUrl = !empty($optipic_options['exclusions_url'])? $optipic_options['exclusions_url']: ''; 
        ?>
        <?php
        // set default 'domains'
        if (empty($optipic_options['domains'])) {
            $homeUrlParse = wp_parse_url(home_url());
            $homeHost = (empty($homeUrlParse['port']))? $homeUrlParse['host']: $homeUrlParse['host'].':'.$homeUrlParse['port'];
            $optipic_options['domains'] = implode("\n", array($homeHost, 'www.'.$homeHost));
        }
        // set default 'srcset attrs'
        if (empty($optipic_options['srcset_attrs'])) {
            $optipic_options['srcset_attrs'] = implode("\n", array('srcset', 'data-srcset'));
        }
        if (empty($optipic_options['whitelist_img_urls'])) {
            $optipic_options['whitelist_img_urls'] = '';
        }
        if (empty($optipic_options['cdn_domain'])) {
            $optipic_options['cdn_domain'] = '';
        }
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?_e("Enable auto-replace image URLs", "optipic")?></th>
                <td>
                    <input type="checkbox" name="optipic_options[cdn_autoreplace_active]"
                           value="Y"
                           <?= (!empty($optipic_options['cdn_autoreplace_active']) && $optipic_options['cdn_autoreplace_active'] == 'Y')? 'checked' : '' ?>
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?_e("Site ID in your personal account CDN OptiPic", "optipic")?></th>
                <td>
                    <input type="text" name="optipic_options[cdn_site_id]" value="<?php echo esc_attr($siteId);?>"/><br/>
                    <small>
                        <? echo str_replace('<a>', '<a href="https://optipic.io/cdn/cp/" target="_blank">', __('You can find out your website ID in <a>your CDN OptiPic personal account</a>. Add your site to your account if you have not already done so.', 'optipic')); ?>
                    </small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?_e("Domain list (if images are loaded via absolute URLs)", "optipic")?></th>
                <td>
                    <textarea type="text" name="optipic_options[domains]" cols="60"><?php echo esc_attr($optipic_options['domains']);?></textarea><br/>
                    <small>
                        <? _e("Each on a new line and without specifying the protocol (http/https). Examples", "optipic"); ?>:
                        <br/>mydomain.com<br/>www.mydomain.com
                    </small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?_e("Site pages that do not include auto-replace", "optipic")?></th>
                <td>
                    <textarea type="text" name="optipic_options[exclusions_url]" cols="60"><?php echo esc_attr($exclusionsUrl);?></textarea><br/>
                    <small>
                        <? _e("Each on a new line and must start with a slash", "optipic"); ?> (/)
                    </small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?_e("Replace only URLs of images starting with a mask", "optipic")?></th>
                <td>
                    <textarea type="text" name="optipic_options[whitelist_img_urls]" cols="60"><?php echo esc_attr($optipic_options['whitelist_img_urls']);?></textarea><br/>
                    <small>
                        <? _e("Each on a new line and must start with a slash", "optipic"); ?> (/). <? _e("Examples", "optipic"); ?>:<br/>
                        /upload/<br/>
                        /upload/test.jpeg
                    </small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?_e("List of 'srcset' attributes", "optipic")?></th>
                <td>
                        <textarea type="text" name="optipic_options[srcset_attrs]" cols="60"><?php echo esc_attr($optipic_options['srcset_attrs']);?></textarea><br/>
                        <small>
                            <? echo str_replace('<a>', '<a href="https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images" target="_blank">', __("List of tag attributes, in which you need to replace srcset-markup of images.<br/><a>What is srcset?</a>", "optipic"))?><br/>
                            <? _e("Examples", "optipic"); ?>:<br/>
                            srcset<br/>
                            data-srcset
                        </small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?_e("CDN domain", "optipic")?></th>
                <td>
                    <input type="text" name="optipic_options[cdn_domain]" value="<?php echo esc_attr($optipic_options['cdn_domain']);?>"/><br/>
                    <small>
                        <? echo __('Domain through which CDN OptiPic will work. You can use your subdomain (img.yourdomain.com, optipic.yourdomain.com, etc.) instead of the standard cdn.optipic.io. To connect your subdomain, contact OptiPic technical support.', 'optipic'); ?>
                    </small>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit"  class="button-primary" value="<?_e("Save Changes")?>"/>
        </p>
    </form>
</div>

<script src="https://optipic.io/api/cp/stat?domain=<?=$_SERVER["HTTP_HOST"]?>&sid=<?=$siteId?>&cms=wordpress&stype=cdn&append_to=<?=urlencode("#optipic-settings-form-wrap")?>&version=<?=optipic_version()?>"></script>