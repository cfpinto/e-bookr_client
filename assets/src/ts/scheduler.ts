const SCHEDULE_MODE_DAY = 'day';

interface RatesApiResponseInterface {
  [key: number]: RatesByDayApiResponseInterface
}

interface RatesByDayApiResponseInterface {
  [key: string]: RateInterface
}

interface RateInterface {
  name: string;
  date: Date;
  room_id: number;
  available: number;
  price: number;
}

interface RoomInterface {
  id: number;
  name: string;
  image: string;
  thumb: string;
  maxAdultCount: number;
  maxChildrenCount: number;
}

class Rate implements RateInterface {
  name: string;
  date: Date;
  room_id: number;
  available: number;
  price: number;
}

interface BookingInterface {
  room?: number;
  start?: Date;
  duration?: number;
  adults?: number;
  children?: number;
}

class Booking implements BookingInterface {
  adults: number = null;
  children: number = null;
  duration: number = null;
  room: number = null;
  start: Date = null;
}

interface SchedulerSettingsInterface {
  elem: HTMLElement;
  pickerForm: HTMLFormElement;
  rooms: Array<RoomInterface>;
  start: Date;
  end: Date;
  duration: number;
  adults: number;
  children?: number;
  src?: string,
  target?: string,
}

class Moment {
  private readonly date: Date;

  public constructor(date: any) {
    if (date instanceof Date) {
      this.date = new Date(date.getTime());
    } else {
      this.date = Moment.toDate(date);
    }
  }

  public format(): string {
    const m = this.date.getMonth() + 1;
    const day = this.date.getDate() > 9 ? this.date.getDate() : '0' + this.date.getDate();
    const month = m > 9 ? m : '0' + m;
    return `${day}/${month}/${this.date.getFullYear()}`;
  }

  public formatToIso(): string {
    const m = this.date.getMonth() + 1;
    const day = this.date.getDate() > 9 ? this.date.getDate() : '0' + this.date.getDate();
    const month = m > 9 ? m : '0' + m;
    return `${this.date.getFullYear()}-${month}-${day}`;
  }

  public getDate(): Date {
    return this.date;
  }

  public toBeginOfMonth(): Moment {
    const date = new Date(this.date.getTime());
    date.setDate(1);
    return new Moment(date);
  }

  public toEndOfMonth(): Moment {
    const date = new Date(this.date);
    const year = date.getFullYear();
    const isLeap = ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)
    if (date.getMonth() == 1 && isLeap) {
      date.setDate(29);
    } else if (date.getMonth() == 1 && !isLeap) {
      date.setDate(28);
    } else if ([0, 2, 4, 6, 7, 9, 11].indexOf(date.getMonth())) {
      date.setDate(31)
    } else {
      date.setDate(30)
    }

    return new Moment(date);
  }

  static toIsoDateString(dateString: string): string {
    let test: RegExpMatchArray = dateString.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})(.*)/);
    if (test) {
      return `${test[3]}-${test[2]}-${test[1]}${test[4]}`;
    }

    //Assume ISO if no match
    return dateString;
  }

  static toDate(dateString: string): Date {
    return new Date(Moment.toIsoDateString(dateString));
  }
}

const moment = (dateString: any): Moment => {
  return new Moment(dateString);
};

class Scheduler {
  private mode: string = SCHEDULE_MODE_DAY;
  public start: Date;
  public end: Date;
  public rooms: Array<RoomInterface>;
  public duration: number = 1;
  public adults: number = 0;
  public children: number = 0;
  public src: string;
  public target: string;

  public constructor(settings: SchedulerSettingsInterface) {
    this.rooms = settings.rooms;
    this.adults = settings.adults;
    this.children = settings.children;
    this.duration = settings.duration;
    this.src = settings.src;
    this.target = settings.target;
    this.setRange(settings.start, settings.end);
    const renderer = new Renderer(settings.elem, settings.pickerForm, this);
    renderer.render();
  }

  public getAjaxSrc(): string {
    return `${this.src}?date_check_in=${moment(this.start).format()}&date_check_out=${moment(this.end).format()}&adults=${this.adults}&children=${this.children}`;
  }

  public getMode(): string {
    return this.mode;
  }

  public setRange(start: Date, end: Date): Scheduler {
    this.start = moment(start).toBeginOfMonth().getDate() > new Date() ? moment(start).toBeginOfMonth().getDate() : new Date();
    this.end = moment(end).toEndOfMonth().getDate();
    this.start.setHours(0, 0, 0, 0);
    this.end.setHours(0, 0, 0, 0);
    return this;
  }

  public getRange(): Array<any> {
    switch (this.mode) {
      default:
      case SCHEDULE_MODE_DAY:
        const arr = [];
        for (let dt = new Date(this.start.getTime()); dt <= this.end; dt.setDate(dt.getDate() + 1)) {
          arr.push(new Date(dt));
        }
        return arr;
    }
    return [];
  }

  public load(): Promise<RatesApiResponseInterface> {
    return new Promise<RatesApiResponseInterface>((resolve, reject) => {
      const ajax: XMLHttpRequest = new XMLHttpRequest();
      ajax.open('GET', this.getAjaxSrc(), true);
      ajax.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
      ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      ajax.withCredentials = true;
      ajax.onreadystatechange = () => {
        if (ajax.readyState === XMLHttpRequest.DONE) {
          if (ajax.status === 200 && (new RegExp('application/json')).test(ajax.getResponseHeader('Content-Type'))) {
            resolve(JSON.parse(ajax.responseText));
          } else {
            reject(ajax.status)
          }
        }
      };

      ajax.addEventListener('error', (evt: ProgressEvent<XMLHttpRequestEventTarget>) => {
        reject(evt);
      });
      ajax.addEventListener('abort', (evt: ProgressEvent<XMLHttpRequestEventTarget>) => {
        reject(evt);
      });
      ajax.send();
    });
  }
}

class Renderer {
  public container: HTMLElement;
  public pickerForm: HTMLFormElement;
  public scheduler: Scheduler;
  public panel: HTMLElement;
  public aside: HTMLElement;
  public header: HTMLElement;
  public action: HTMLButtonElement;
  public width: number = 0;
  public days: Array<string> = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  public months: Array<string> = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  private timeout;
  private booking: Booking;

  constructor(container: HTMLElement, pickerForm: HTMLFormElement, scheduler: Scheduler) {
    this.container = container;
    this.scheduler = scheduler;
    this.pickerForm = pickerForm;
  }

  private scrolling(evt): void {
    evt.stopPropagation();
  }

  private hasEnoughDays(evt): boolean {
    let elem: any = evt.target;
    let count = 1;

    while ((elem = elem.nextSibling) && !/taken/.test(elem.className) && count < this.scheduler.duration) {
      count++;
      elem.className += ' hover'
    }

    return !(count < this.scheduler.duration);
  }

  private cellMouseEnter(evt): void {
    evt.stopPropagation();
    if (!this.hasEnoughDays(evt)) {
      evt.target.parentElement.querySelectorAll('.hover').forEach((item: HTMLDivElement) => {
        item.className = item.className.replace(' hover', '');
      });
      evt.target.removeEventListener('click', this.cellClicked.bind(this));
    } else {
      evt.target.className += ' can-select';
      evt.target.removeEventListener('click', this.cellClicked.bind(this));
      evt.target.addEventListener('click', this.cellClicked.bind(this));
    }
  }

  private cellClicked(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    //remove all previous selected classes
    const selected = this.panel.querySelectorAll('.selected');
    const toSelect = this.panel.querySelectorAll('.hover,.can-select');
    for (let i = 0; i < selected.length; i++) {
      selected[i].className = selected[i].className.replace('selected', '');
    }

    window.setTimeout(() => {
      for (let i = 0; i < toSelect.length; i++) {
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
  }

  private cellMouseLeave(evt): void {
    evt.stopPropagation();
    this.panel.querySelectorAll('.cell').forEach((item: HTMLDivElement) => {
      item.className = item.className
        .replace(' hover', '')
        .replace(' can-select', '');
    });
  }

  private addClassName(): void {
    if (!/scheduler/.test(this.container.className)) {
      this.container.className += 'scheduler';
    }
  }

  private renderAside(): void {
    this.aside = document.createElement('aside');
    this.aside.className = 'aside';
    this.container.appendChild(this.aside);

    this.scheduler.rooms.map(item => {
      const elem: HTMLDivElement = document.createElement('div');
      const img: HTMLImageElement = document.createElement('img');
      const text: Text = new Text(item.name);
      img.src = item.thumb;
      img.setAttribute('data-img', item.image);
      elem.appendChild(img);
      elem.appendChild(text);
      this.aside.appendChild(elem);
    })

  }

  private renderPanel(): void {
    this.panel = document.createElement('div');
    this.panel.className = 'right';
    this.panel.addEventListener('scroll', this.scrolling.bind(this));
    this.container.appendChild(this.panel);
  }

  private renderAction(): void {
    const actionPanel = document.createElement('div');
    actionPanel.className = 'book-now';
    this.action = document.createElement('button');
    this.action.textContent = 'Book Now';
    this.action.type = 'button';
    actionPanel.appendChild(this.action);
    this.container.appendChild(actionPanel);
    this.action.addEventListener('click', () => {
      window.location.href = this.scheduler.target + '?room=' + this.booking.room + '&start=' + moment(this.booking.start).formatToIso() + '&duration=' + this.booking.duration + '&adults=' + this.booking.adults + '&children=' + this.booking.children;
    })
  }

  private renderSlots(data: RatesApiResponseInterface = []): void {
    while (this.panel.getElementsByClassName('line').length > 0) {
      this.panel.removeChild(this.panel.querySelector('.line'));
    }

    this.scheduler.rooms.map((room: RoomInterface) => {
      const line: HTMLElement = document.createElement('div');
      const events: RatesByDayApiResponseInterface = data[room.id] || {};
      this.panel.appendChild(line);
      line.className = 'line';
      line.style.width = this.width.toString() + 'px';
      this.scheduler.getRange().map((date: Date) => {
        const cell: HTMLElement = document.createElement('div');
        const today: string = date.toISOString().slice(0, 10);
        cell.className = 'cell day-week-' + date.getDay() + (this.isNotAvailable(events[today] || new Rate(), room) ? ' taken' : '');
        cell.setAttribute('data-room', room.id.toString());
        cell.setAttribute('data-date', date.toISOString().substr(0, 10));
        if (events[today] && events[today].available == 1) {
          cell.innerHTML = '&euro;' + events[today].price;
          cell.addEventListener('mouseenter', this.cellMouseEnter.bind(this));
          cell.addEventListener('mouseleave', this.cellMouseLeave.bind(this));
        }
        line.appendChild(cell);
      });
    })
  }

  private renderHeader(): void {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (!this.header) {
      this.header = document.createElement('div');
      this.header.className = 'header';
    } else {
      this.header.innerHTML = '';
    }
    this.panel.appendChild(this.header);
    this.scheduler.getRange().map((date: Date) => {
      const cell: HTMLElement = document.createElement('div');
      cell.className = 'cell day-week-' + date.getDay() + (date.getTime() == today.getTime() ? ' today' : '');
      cell.innerHTML = `${date.getDate().toString()} ${this.months[date.getMonth()]}<small>${this.days[date.getDay()]}</small>`;
      this.header.appendChild(cell);
      this.width += cell.offsetWidth;
    });

    this.header.style.width = this.width.toString() + 'px';
  }

  private isNotAvailable(rate: RateInterface, room: RoomInterface): boolean {
    return rate.available == 0 || rate.price <= 0 ||
      room.maxAdultCount < this.scheduler.adults ||
      room.maxChildrenCount < this.scheduler.children;
  }

  private bindDatePickers(): void {
    const startDate: HTMLInputElement = this.pickerForm.querySelector('[name=date_check_in]');
    const endDate: HTMLInputElement = this.pickerForm.querySelector('[name=date_check_out]');
    const adults: HTMLInputElement = this.pickerForm.querySelector('[name=adults]');
    const children: HTMLInputElement = this.pickerForm.querySelector('[name=children]');
    const onValueChange = e => {
      // this.scheduler.start = 
      e.stopPropagation();
      this.scheduler.children = parseInt(children.value);
      this.scheduler.adults = parseInt(adults.value);
      this.scheduler.setRange(moment(startDate.value).getDate(), moment(endDate.value).getDate());
      window.clearTimeout(this.timeout);
      this.timeout = window.setTimeout(() => {
        this.renderDates();
      }, 300)
    };

    this.pickerForm.onsubmit = e => {
      e.stopPropagation();
      e.preventDefault();
    };

    startDate.onchange = onValueChange.bind(startDate);
    endDate.onchange = onValueChange.bind(startDate);
    adults.onchange = onValueChange.bind(startDate);
    children.onchange = onValueChange.bind(startDate);
  }

  public renderDates(): void {
    this.action.disabled = true;
    this.booking = new Booking();
    this.renderHeader();
    this.renderSlots([]);
    this.scheduler
      .load()
      .then((data: RatesApiResponseInterface) => {
        this.renderSlots(data)
      });
  }

  public render() {
    this.addClassName();
    this.renderAside();
    this.renderPanel();
    this.renderAction();
    this.bindDatePickers();
    this.renderDates();
  }
}