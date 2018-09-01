<div>
    <div class="uk-card uk-card-%%cardtype%%">
        <div class="uk-card-header %%background%%">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-1-3m">
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="ifttt" value="1">
                        <input type="hidden" name="action" value="On">
                        <input type="hidden" name="device" value="%%devicename%%">
                        <button class="uk-text-middle uk-margin-right uk-button uk-button-text" uk-tooltip="Turn on" type="submit" name="nothing" value=""><span uk-icon="icon: bolt"></span></button>
                    </form>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="ifttt" value="1">
                        <input type="hidden" name="action" value="Off">
                        <input type="hidden" name="device" value="%%devicename%%">
                        <button class="uk-text-middle uk-margin-right uk-button uk-button-text" uk-tooltip="Turn off" type="submit" name="nothing" value=""><span uk-icon="icon: ban"></span></button>
                    </form>
                    <h3 class="uk-card-title uk-margin-remove-bottom">%%devicename%%</h3>
                    <p class="uk-text-meta uk-margin-remove-top"><span class="uk-text-bold">Id</span>: %%deviceId%%</p>
                </div>
            </div>
        </div>
        <form class="uk-form-horizontal" method="post">
            <input type="hidden" name="deviceid" value="%%deviceId%%">
            <input type="hidden" name="action" value="savedevice">
            <div class="uk-card-body">
                <p><span class="uk-text-bold">Device type</span><BR>%%devicetypeName%%</p>
                <p><label class="uk-text-bold" for="type">Your type</label>%%deviceType%%</p>
                <p><label class="uk-text-bold" for="room">Room</label>%%deviceRoom%%</p>
            </div>
            <div class="uk-card-footer uk-text-right">
                <input type="submit" class="uk-button uk-button-default uk-text-bold" value="Save">
            </div>
        </form>
    </div>
</div>
