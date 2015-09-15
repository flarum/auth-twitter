import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import saveConfig from 'flarum/utils/saveConfig';

export default class TwitterSettingsModal extends Modal {
  constructor(...args) {
    super(...args);

    this.apiKey = m.prop(app.config['twitter.api_key'] || '');
    this.apiSecret = m.prop(app.config['twitter.api_secret'] || '');
  }

  className() {
    return 'TwitterSettingsModal Modal--small';
  }

  title() {
    return 'TwiTter Settings';
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="Form">
          <div className="Form-group">
            <label>API Key</label>
            <input className="FormControl" value={this.apiKey()} oninput={m.withAttr('value', this.apiKey)}/>
          </div>

          <div className="Form-group">
            <label>API Secret</label>
            <input className="FormControl" value={this.apiSecret()} oninput={m.withAttr('value', this.apiSecret)}/>
          </div>

          <div className="Form-group">
            <Button
              type="submit"
              className="Button Button--primary TwitterSettingsModal-save"
              loading={this.loading}>
              Save Changes
            </Button>
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();

    this.loading = true;

    saveConfig({
      'twitter.api_key': this.apiKey(),
      'twitter.api_secret': this.apiSecret()
    }).then(
      () => this.hide(),
      () => {
        this.loading = false;
        m.redraw();
      }
    );
  }
}
