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





import Helper from './helper';
window.Helper = Helper;

import Form from './utilities/form';
window.Form = Form;

import Auth from './models/auth';
import Menus from './models/menus';
import Term from './models/term';
import Teacher from './models/teacher';
import TeacherGroup from './models/teacherGroup';
import Wage from './models/wage';
import Volunteer from './models/volunteer';
import User from './models/user';
import Account from './models/account';
import Photo from './models/photo';
import Admin from './models/admin';
import Center from './models/center';
import Category from './models/category';
import ContactInfo from './models/contactInfo';
import Course from './models/course';
import ClassTime from './models/classTime';
import Process from './models/process';
import Lesson from './models/lesson';
import Payroll from './models/payroll';
import Signup from './models/signup';
import Bill from './models/bill';
import Quit from './models/quit';
import Student from './models/student';
import Tran from './models/tran';
import Files from './models/files';

window.Auth=Auth;
window.Menus=Menus;
window.Term=Term;
window.Teacher=Teacher;
window.TeacherGroup=TeacherGroup;
window.Wage=Wage;
window.Volunteer=Volunteer;
window.User=User;
window.Account=Account;
window.Photo=Photo;
window.Admin=Admin;
window.Center=Center;
window.Category=Category;
window.ContactInfo=ContactInfo;
window.Course=Course;
window.ClassTime=ClassTime;
window.Process=Process;
window.Lesson=Lesson;
window.Payroll=Payroll;
window.Signup=Signup;
window.Bill=Bill;
window.Quit=Quit;
window.Student=Student;
window.Tran=Tran;
window.Files=Files;

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

Vue.filter('quitStatusLabel', (status)=> {
    return Quit.statusLabel(status)
});

Vue.filter('payrollStatusLabel', (status)=> {
    return Payroll.statusLabel(status)
});


