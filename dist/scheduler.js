var SCHEDULE_MODE_DAY = 'day';
var Rate = /** @class */ (function () {
    function Rate() {
    }
    return Rate;
}());
var Booking = /** @class */ (function () {
    function Booking() {
        this.adults = null;
        this.children = null;
        this.duration = null;
        this.room = null;
        this.start = null;
    }
    return Booking;
}());
var Moment = /** @class */ (function () {
    function Moment(date) {
        if (date instanceof Date) {
            this.date = new Date(date.getTime());
        }
        else {
            this.date = Moment.toDate(date);
        }
    }
    Moment.prototype.format = function () {
        var m = this.date.getMonth() + 1;
        var day = this.date.getDate() > 9 ? this.date.getDate() : '0' + this.date.getDate();
        var month = m > 9 ? m : '0' + m;
        return day + "/" + month + "/" + this.date.getFullYear();
    };
    Moment.prototype.formatToIso = function () {
        var m = this.date.getMonth() + 1;
        var day = this.date.getDate() > 9 ? this.date.getDate() : '0' + this.date.getDate();
        var month = m > 9 ? m : '0' + m;
        return this.date.getFullYear() + "-" + month + "-" + day;
    };
    Moment.prototype.getDate = function () {
        return this.date;
    };
    Moment.prototype.toBeginOfMonth = function () {
        var date = new Date(this.date.getTime());
        date.setDate(1);
        return new Moment(date);
    };
    Moment.prototype.toEndOfMonth = function () {
        var date = new Date(this.date);
        var year = date.getFullYear();
        var isLeap = ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0);
        if (date.getMonth() == 1 && isLeap) {
            date.setDate(29);
        }
        else if (date.getMonth() == 1 && !isLeap) {
            date.setDate(28);
        }
        else if ([0, 2, 4, 6, 7, 9, 11].indexOf(date.getMonth())) {
            date.setDate(31);
        }
        else {
            date.setDate(30);
        }
        return new Moment(date);
    };
    Moment.toIsoDateString = function (dateString) {
        var test = dateString.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})(.*)/);
        if (test) {
            return test[3] + "-" + test[2] + "-" + test[1] + test[4];
        }
        //Assume ISO if no match
        return dateString;
    };
    Moment.toDate = function (dateString) {
        return new Date(Moment.toIsoDateString(dateString));
    };
    return Moment;
}());
var moment = function (dateString) {
    return new Moment(dateString);
};
var Scheduler = /** @class */ (function () {
    function Scheduler(settings) {
        this.mode = SCHEDULE_MODE_DAY;
        this.duration = 1;
        this.adults = 0;
        this.children = 0;
        this.rooms = settings.rooms;
        this.adults = settings.adults;
        this.children = settings.children;
        this.duration = settings.duration;
        this.src = settings.src;
        this.target = settings.target;
        this.setRange(settings.start, settings.end);
        var renderer = new Renderer(settings.elem, settings.pickerForm, this);
        renderer.render();
    }
    Scheduler.prototype.getAjaxSrc = function () {
        return this.src + "?date_check_in=" + moment(this.start).format() + "&date_check_out=" + moment(this.end).format() + "&adults=" + this.adults + "&children=" + this.children;
    };
    Scheduler.prototype.getMode = function () {
        return this.mode;
    };
    Scheduler.prototype.setRange = function (start, end) {
        this.start = moment(start).toBeginOfMonth().getDate() > new Date() ? moment(start).toBeginOfMonth().getDate() : new Date();
        this.end = moment(end).toEndOfMonth().getDate();
        this.start.setHours(0, 0, 0, 0);
        this.end.setHours(0, 0, 0, 0);
        return this;
    };
    Scheduler.prototype.getRange = function () {
        switch (this.mode) {
            default:
            case SCHEDULE_MODE_DAY:
                var arr = [];
                for (var dt = new Date(this.start.getTime()); dt <= this.end; dt.setDate(dt.getDate() + 1)) {
                    arr.push(new Date(dt));
                }
                return arr;
        }
        return [];
    };
    Scheduler.prototype.load = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var ajax = new XMLHttpRequest();
            ajax.open('GET', _this.getAjaxSrc(), true);
            ajax.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            ajax.withCredentials = true;
            ajax.onreadystatechange = function () {
                if (ajax.readyState === XMLHttpRequest.DONE) {
                    if (ajax.status === 200 && (new RegExp('application/json')).test(ajax.getResponseHeader('Content-Type'))) {
                        resolve(JSON.parse(ajax.responseText));
                    }
                    else {
                        reject(ajax.status);
                    }
                }
            };
            ajax.addEventListener('error', function (evt) {
                reject(evt);
            });
            ajax.addEventListener('abort', function (evt) {
                reject(evt);
            });
            ajax.send();
        });
    };
    return Scheduler;
}());
var Renderer = /** @class */ (function () {
    function Renderer(container, pickerForm, scheduler) {
        this.width = 0;
        this.days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        this.months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        this.container = container;
        this.scheduler = scheduler;
        this.pickerForm = pickerForm;
    }
    Renderer.prototype.scrolling = function (evt) {
        evt.stopPropagation();
    };
    Renderer.prototype.hasEnoughDays = function (evt) {
        var elem = evt.target;
        var count = 1;
        while ((elem = elem.nextSibling) && !/taken/.test(elem.className) && count < this.scheduler.duration) {
            count++;
            elem.className += ' hover';
        }
        return !(count < this.scheduler.duration);
    };
    Renderer.prototype.cellMouseEnter = function (evt) {
        evt.stopPropagation();
        if (!this.hasEnoughDays(evt)) {
            evt.target.parentElement.querySelectorAll('.hover').forEach(function (item) {
                item.className = item.className.replace(' hover', '');
            });
            evt.target.removeEventListener('click', this.cellClicked.bind(this));
        }
        else {
            evt.target.className += ' can-select';
            evt.target.removeEventListener('click', this.cellClicked.bind(this));
            evt.target.addEventListener('click', this.cellClicked.bind(this));
        }
    };
    Renderer.prototype.cellClicked = function (evt) {
        evt.stopPropagation();
        evt.preventDefault();
        //remove all previous selected classes
        var selected = this.panel.querySelectorAll('.selected');
        var toSelect = this.panel.querySelectorAll('.hover,.can-select');
        for (var i = 0; i < selected.length; i++) {
            selected[i].className = selected[i].className.replace('selected', '');
        }
        window.setTimeout(function () {
            for (var i = 0; i < toSelect.length; i++) {
                toSelect[i].className = toSelect[i].className
                    .replace('hover', 'selected')
                    .replace('can-select', 'selected')
                    .trim();
            }
        }, 300);
        this.booking.room = evt.target.getAttribute('data-room');
        this.booking.start = moment(evt.target.getAttribute('data-date')).getDate();
        this.booking.duration = this.scheduler.duration;
        this.booking.adults = this.scheduler.adults;
        this.booking.children = this.scheduler.children;
        this.action.disabled = false;
    };
    Renderer.prototype.cellMouseLeave = function (evt) {
        evt.stopPropagation();
        this.panel.querySelectorAll('.cell').forEach(function (item) {
            item.className = item.className
                .replace(' hover', '')
                .replace(' can-select', '');
        });
    };
    Renderer.prototype.addClassName = function () {
        if (!/scheduler/.test(this.container.className)) {
            this.container.className += 'scheduler';
        }
    };
    Renderer.prototype.renderAside = function () {
        var _this = this;
        this.aside = document.createElement('aside');
        this.aside.className = 'aside';
        this.container.appendChild(this.aside);
        this.scheduler.rooms.map(function (item) {
            var elem = document.createElement('div');
            var img = document.createElement('img');
            var text = new Text(item.name);
            img.src = item.thumb;
            img.setAttribute('data-img', item.image);
            elem.appendChild(img);
            elem.appendChild(text);
            _this.aside.appendChild(elem);
        });
    };
    Renderer.prototype.renderPanel = function () {
        this.panel = document.createElement('div');
        this.panel.className = 'right';
        this.panel.addEventListener('scroll', this.scrolling.bind(this));
        this.container.appendChild(this.panel);
    };
    Renderer.prototype.renderAction = function () {
        var _this = this;
        var actionPanel = document.createElement('div');
        actionPanel.className = 'book-now';
        this.action = document.createElement('button');
        this.action.textContent = 'Book Now';
        this.action.type = 'button';
        actionPanel.appendChild(this.action);
        this.container.appendChild(actionPanel);
        this.action.addEventListener('click', function () {
            window.location.href = _this.scheduler.target + '?room=' + _this.booking.room + '&start=' + moment(_this.booking.start).formatToIso() + '&duration=' + _this.booking.duration + '&adults=' + _this.booking.adults + '&children=' + _this.booking.children;
        });
    };
    Renderer.prototype.renderSlots = function (data) {
        var _this = this;
        if (data === void 0) { data = []; }
        while (this.panel.getElementsByClassName('line').length > 0) {
            this.panel.removeChild(this.panel.querySelector('.line'));
        }
        this.scheduler.rooms.map(function (room) {
            var line = document.createElement('div');
            var events = data[room.id] || {};
            _this.panel.appendChild(line);
            line.className = 'line';
            line.style.width = _this.width.toString() + 'px';
            _this.scheduler.getRange().map(function (date) {
                var cell = document.createElement('div');
                var today = date.toISOString().slice(0, 10);
                cell.className = 'cell day-week-' + date.getDay() + (_this.isNotAvailable(events[today] || new Rate(), room) ? ' taken' : '');
                cell.setAttribute('data-room', room.id.toString());
                cell.setAttribute('data-date', date.toISOString().substr(0, 10));
                if (events[today] && events[today].available == 1) {
                    cell.innerHTML = '&euro;' + events[today].price;
                    cell.addEventListener('mouseenter', _this.cellMouseEnter.bind(_this));
                    cell.addEventListener('mouseleave', _this.cellMouseLeave.bind(_this));
                }
                line.appendChild(cell);
            });
        });
    };
    Renderer.prototype.renderHeader = function () {
        var _this = this;
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        if (!this.header) {
            this.header = document.createElement('div');
            this.header.className = 'header';
        }
        else {
            this.header.innerHTML = '';
        }
        this.panel.appendChild(this.header);
        this.scheduler.getRange().map(function (date) {
            var cell = document.createElement('div');
            cell.className = 'cell day-week-' + date.getDay() + (date.getTime() == today.getTime() ? ' today' : '');
            cell.innerHTML = date.getDate().toString() + " " + _this.months[date.getMonth()] + "<small>" + _this.days[date.getDay()] + "</small>";
            _this.header.appendChild(cell);
            _this.width += cell.offsetWidth;
        });
        this.header.style.width = this.width.toString() + 'px';
    };
    Renderer.prototype.isNotAvailable = function (rate, room) {
        return rate.available == 0 || rate.price <= 0 ||
            room.maxAdultCount < this.scheduler.adults ||
            room.maxChildrenCount < this.scheduler.children;
    };
    Renderer.prototype.bindDatePickers = function () {
        var _this = this;
        var startDate = this.pickerForm.querySelector('[name=date_check_in]');
        var endDate = this.pickerForm.querySelector('[name=date_check_out]');
        var adults = this.pickerForm.querySelector('[name=adults]');
        var children = this.pickerForm.querySelector('[name=children]');
        var onValueChange = function (e) {
            // this.scheduler.start = 
            e.stopPropagation();
            _this.scheduler.children = parseInt(children.value);
            _this.scheduler.adults = parseInt(adults.value);
            _this.scheduler.setRange(moment(startDate.value).getDate(), moment(endDate.value).getDate());
            window.clearTimeout(_this.timeout);
            _this.timeout = window.setTimeout(function () {
                _this.renderDates();
            }, 300);
        };
        this.pickerForm.onsubmit = function (e) {
            e.stopPropagation();
            e.preventDefault();
        };
        startDate.onchange = onValueChange.bind(startDate);
        endDate.onchange = onValueChange.bind(startDate);
        adults.onchange = onValueChange.bind(startDate);
        children.onchange = onValueChange.bind(startDate);
    };
    Renderer.prototype.renderDates = function () {
        var _this = this;
        this.action.disabled = true;
        this.booking = new Booking();
        this.renderHeader();
        this.renderSlots([]);
        this.scheduler
            .load()
            .then(function (data) {
            _this.renderSlots(data);
        });
    };
    Renderer.prototype.render = function () {
        this.addClassName();
        this.renderAside();
        this.renderPanel();
        this.renderAction();
        this.bindDatePickers();
        this.renderDates();
    };
    return Renderer;
}());
//# sourceMappingURL=scheduler.js.map