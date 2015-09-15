import { extend } from 'flarum/extend';
import app from 'flarum/app';
import LogInButtons from 'flarum/components/LogInButtons';
import LogInButton from 'flarum/components/LogInButton';

app.initializers.add('twitter', () => {
  extend(LogInButtons.prototype, 'items', function(items) {
    items.add('twitter',
      <LogInButton
        className="Button LogInButton--twitter"
        icon="twitter"
        path="/login/twitter">
        Log in with Twitter
      </LogInButton>
    );
  });
});
