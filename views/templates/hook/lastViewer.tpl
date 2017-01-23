<div id="viewed-products_block_left" class="block products_block">
    <h4 class="title_block">{l s='Modulo Personalizado' mod='blockviewed'}</h4>
    <div class="block_content">
        <ul class="products clearfix">
            {foreach from=$productsViewedObj item=viewedProduct name=myLoop}
                <li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
                    <div class="text_desc">
                        <h5 class="s_title_block"><a href="{$viewedProduct->product_link|escape:'html'}" title="{l s='About' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->name|truncate:14:'...'|escape:html:'UTF-8'}</a></h5>
                        <p><a href="{$viewedProduct->product_link|escape:'html'}" title="{l s='About' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->description_short|strip_tags:'UTF-8'|truncate:44}</a></p>
                        <p>{$viewedProduct->precio|strip_tags:'UTF-8'}</p>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
</div>