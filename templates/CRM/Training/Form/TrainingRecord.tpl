{crmScope extensionKey='com.graydigitalgroup.training'}
{* HEADER *}
{if $action eq 8}
   <div class="crm-block crm-form-block crm-data-processor_source-block">
    <div class="crm-section">
      <p>Are you sure you want to delete this Training Record?</p>
    </div>
  </div>
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
  </div>
{else}
  <div class="crm-block crm-form-block crm-data-processor_source-block">
    <div class="crm-section">
      <div class="label">{$form.type_id.label}</div>
      <div class="content">{$form.type_id.html}</div>
      <div class="clear"></div>
    </div>

    <div class="crm-section">
      <div class="label">{$form.label.label}</div>
      <div class="content">{$form.label.html}</div>
      <div class="clear"></div>
    </div>

    <div class="crm-section">
      <div class="label">{$form.description.label}</div>
      <div class="content">{$form.description.html}</div>
      <div class="clear"></div>
    </div>

    <div class="crm-section credits-awarded">
      <div class="label">{$form.credits_awarded.label}</div>
      <div class="content">{$form.credits_awarded.html}</div>
      <div class="clear"></div>
    </div>

    <div class="crm-section credits" style="display:none;">
      <div class="label">{$form.credits.label}</div>
      <div class="content">{$form.credits.html}</div>
      <div class="clear"></div>
    </div>

    <div class="crm-section">
      <div class="label">{$form.entry_date.label}</div>
      <div class="content">{$form.entry_date.html}</div>
      <div class="clear"></div>
    </div>
  </div>


  {* FOOTER *}
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

  {literal}
  <script type="text/javascript">
  (function($) {
    var creditsAwarded = {/literal}{$credits_awarded}{literal};
    if ( 1 == $('.credits-awarded input[type=radio]:checked').val() ) {
      $('.credits').show();
    }
    $('.credits-awarded input[type=radio]').on('change', function(){
      if ( 1 == $(this).val() ) {
        $('.credits').show();
      } else {
        $('.credits').hide();
      }
    })
  })(CRM.$);
  </script>
  {/literal}
{/if}
{/crmScope}
