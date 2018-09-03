<div class="uk-vertical-align-middle uk-card">
    <h1 class="uk-heading-line uk-padding-large"><span>Creating IFTTT Applets</span></h1>
    <div class="uk-card uk-card-default uk-body uk-padding">
        <h2>Turn on/off all devices of a certain type in a room</h2>
        <h4>Choose the room and type of device</h4>
        <p>
        <form method="post">
            <input type="hidden" name="action" value="iftttwizard2">
            <input type="hidden" name="ifttt-type" value="room">
            <label for="ifttt-chosen-room">Room</label>
            <select name="ifttt-chosen-room" class="uk-select">
                %%list1%%
            </select>
            <label for="ifttt-chosen-type">Type</label>
            <select name="ifttt-chosen-type" class="uk-select">
                %%list2%%
            </select>
            <input class="uk-button uk-button-primary uk-width-1-1 uk-margin-top" type="submit" value="next">
        </form>
        </p>
    </div>
</div>
