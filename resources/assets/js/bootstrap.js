import Vue from 'vue';
window.Vue = Vue;

window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });




window.Helper = require('./helper');
window.Helper = require('./utilities/form');
window.Menus = require('./models/menus');
window.Term = require('./models/term');
window.Teacher = require('./models/teacher');
window.TeacherGroup = require('./models/teacherGroup');
window.User = require('./models/user');
window.Admin = require('./models/admin');
window.Center = require('./models/center');
window.Category = require('./models/category');
window.ContactInfo = require('./models/contactInfo');
window.Course = require('./models/course');
window.ClassTime = require('./models/classTime');
window.Process = require('./models/process');
window.Signup = require('./models/signup');
window.Bill = require('./models/bill');
window.Student = require('./models/student');
window.Files = require('./models/files');

window.Bus = new Vue({});


Vue.filter('genderText', (gender)=> {
    if (Helper.isTrue(gender)) return '男';
    return '女';
});
Vue.filter('activeLabel', (active)=> {
    return Helper.activeLabel(active);

});
Vue.filter('reviewedLabel', (reviewed)=> {
    return Helper.reviewedLabel(reviewed);
});

Vue.filter('courseActiveLabel', (active)=> {
    return Course.activeLabel(active);
});

Vue.filter('formatMoney', (val)=> {
    return Helper.formatMoney(val);
});

Vue.filter('classTimesHtml', (course)=> {
    return Course.classTimesHtml(course)
});

Vue.filter('signupStatusLabel', (status)=> {
    return Signup.statusLabel(status)
});


