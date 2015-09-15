import app from 'flarum/app';

import TwitterSettingsModal from 'twitter/components/TwitterSettingsModal';

app.initializers.add('twitter', () => {
  app.extensionSettings.twitter = () => app.modal.show(new TwitterSettingsModal());
});
