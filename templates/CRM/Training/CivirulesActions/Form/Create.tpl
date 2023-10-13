{crmScope key='com.graydigitalgroup.training'}
<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-email-send">
	<div class="help-block" id="help">
	{ts}
		<p>This form is used to set the training record data.</p>
		<p>The fields are as follows:
		<ol>
		<li>Training Type: (required) This is the training type to be set for the record. Please select from the list of currently available types.</li>
		{if ($hasEvent)}
		<li>Use Event as Label: Since this trigger is based on an Event, you can choose to use the name of the event as Training Name for this record.</li>
		<li>Training Name: If you do not choose to use the Event title as the Training Name, you will need to manually set the Training Name that will be used for all records for this trigger.</li>
		{else}
		<li>Training Name: (required) Set the Training Name that will be used for all records for this trigger.</li>
		{/if}
		<li>Training Description: This field is used to set some notes for this record.</li>
		<li>Award Credits: Does this training record need to record any credits.</li>
		<li>Credits Awarded: If the training record is to award credits, you need to enter the number of credits to be awarded.</li>
		</ol>
		</p>
		<p>When the trigger is fired, the training record will be set with the values provided from the form.</p>
	{/ts}
	</div>

	<div class="crm-section">
		<div class="label">{$form.type_id.label}</div>
		<div class="content">{$form.type_id.html}</div>
		<div class="clear"></div>
	</div>

	<div class="crm-section {if (!$hasEvent)}hiddenElement{/if} useEvent">
		<div class="label">{$form.use_event.label}</div>
		<div class="content">{$form.use_event.html}</div>
		<div class="clear"></div>
	</div>

	<div class="crm-section record-name">
		<div class="label">{$form.label.label}</div>
		<div class="content">{$form.label.html}</div>
		<div class="clear"></div>
	</div>

	<div class="crm-section">
		<div class="label">{$form.description.label}</div>
		<div class="content">{$form.description.html}</div>
		<div class="clear"></div>
	</div>

	<div class="crm-section credits_awarded">
		<div class="label">{$form.credits_awarded.label}</div>
		<div class="content">{$form.credits_awarded.html}</div>
		<div class="clear"></div>
	</div>

	<div class="crm-section {if (!$credits_awarded)}hiddenElement{/if} credits">
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
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
	<script type="text/javascript">
		cj(function() {
			cj('input:radio[name=credits_awarded]').change(triggerAlternativeReceiverChange);
			//triggerAlternativeReceiverChange();
			cj('.useEvent input[type=checkbox]').change( function() {
				if (this.checked) {
					cj('.record-name').addClass('hiddenElement');
				} else {
					cj('.record-name').removeClass('hiddenElement');
				}
			});
			if (!cj('.useEvent').hasClass('hiddenElement') && cj('.useEvent input[type=checkbox]').is(':checked')) {
				cj('.record-name').addClass('hiddenElement');
			} else {
				cj('.record-name').removeClass('hiddenElement');
			}
		});
		function triggerAlternativeReceiverChange() {
			cj('.crm-section.credits').addClass('hiddenElement');
			if (this.value == 1) {
				cj('.crm-section.credits').removeClass('hiddenElement');
			}
		}
	</script>
{/literal}
{/crmScope}
