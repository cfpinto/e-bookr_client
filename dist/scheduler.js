var SCHEDULE_MODE_DAY = 'day';
var Rate = /** @class */ (function () {
    function Rate() {
    }
    return Rate;
}());
var Scheduler = /** @class */ (function () {
    function Scheduler(settings) {
        this.mode = SCHEDULE_MODE_DAY;
        this.duration = 1;
        this.adults = 0;
        this.children = 0;
        this.rooms = settings.rooms;
        this.start = settings.start;
        this.adults = settings.adults;
        this.children = settings.children;
        this.duration = settings.duration;
        this.src = settings.src;
        this.target = settings.target;
        this.setRange(settings.start, settings.end);
        var renderer = new Renderer(settings.elem, this);
        renderer.render();
    }
    Scheduler.prototype.getMode = function () {
        return this.mode;
    };
    Scheduler.prototype.setRange = function (start, end) {
        this.start = start > new Date() ? start : new Date();
        this.end = end;
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
            ajax.open('GET', _this.src, true);
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
    function Renderer(container, scheduler) {
        this.width = 0;
        this.days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        this.container = container;
        this.scheduler = scheduler;
    }
    Renderer.prototype.scrolling = function (evt) {
        evt.stopPropagation();
    };
    Renderer.prototype.cellMouseEnter = function (evt) {
        evt.stopPropagation();
        var elem = evt.target;
        var count = 1;
        while ((elem = elem.nextSibling) && !/taken/.test(elem.className) && count < this.scheduler.duration) {
            count++;
            elem.className += ' hover';
        }
        if (count < this.scheduler.duration) {
            evt.target.parentElement.querySelectorAll('.hover').forEach(function (item) {
                item.className = item.className.replace(' hover', '');
            });
            evt.target.removeEventListener('click', this.cellClicked.bind(this));
        }
        else {
            evt.target.className += ' can-select';
            evt.target.addEventListener('click', this.cellClicked.bind(this));
        }
    };
    Renderer.prototype.cellClicked = function (evt) {
        evt.stopPropagation();
        window.location.href = this.scheduler.target + '?room=' + evt.target.getAttribute('data-room') + '&start=' + evt.target.getAttribute('data-date') + '&duration=' + this.scheduler.duration + '&adults=' + this.scheduler.adults + '&children=' + this.scheduler.children;
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
        this.header = document.createElement('div');
        this.header.className = 'header';
        this.panel.appendChild(this.header);
        this.scheduler.getRange().map(function (date) {
            var cell = document.createElement('div');
            cell.className = 'cell day-week-' + date.getDay();
            cell.innerText = _this.days[date.getDay()] + ' ' + date.getDate().toString() + '/' + (date.getMonth() + 1).toString();
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
    Renderer.prototype.render = function () {
        var _this = this;
        this.addClassName();
        this.renderAside();
        this.renderPanel();
        this.renderHeader();
        this.scheduler
            .load()
            .then(function (data) {
            _this.renderSlots(data);
        });
    };
    return Renderer;
}());
//# sourceMappingURL=scheduler.js.map