// import { startStimulusApp } from '@symfony/stimulus-bundle';

// const app = startStimulusApp();
// // register any custom, 3rd party controllers here
// // app.register('some_controller_name', SomeImportedController);


import { Application } from '@hotwired/stimulus';
import HelloController from './controllers/hello_controller';

const application = Application.start();
application.register('hello', HelloController);