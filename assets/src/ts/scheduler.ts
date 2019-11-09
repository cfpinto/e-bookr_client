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

interface SchedulerSettingsInterface {
  elem: HTMLElement;
  rooms: Array<RoomInterface>;
  start: Date;
  end: Date;
  duration: number;
  adults: number;
  children?: number;
  src?: string,
  target?: string,
}

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
    this.start = settings.start;
    this.adults = settings.adults;
    this.children = settings.children;
    this.duration = settings.duration;
    this.src = settings.src;
    this.target = settings.target;
    this.setRange(settings.start, settings.end);
    const renderer = new Renderer(settings.elem, this);
    renderer.render();
  }

  public getMode(): string {
    return this.mode;
  }

  public setRange(start: Date, end: Date): Scheduler {
    this.start = start > new Date() ? start : new Date();
    this.end = end;
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
      ajax.open('GET', this.src, true);
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
  public scheduler: Scheduler;
  public panel: HTMLElement;
  public aside: HTMLElement;
  public header: HTMLElement;
  public width: number = 0;
  public days: Array<string> = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  constructor(container, scheduler: Scheduler) {
    this.container = container;
    this.scheduler = scheduler;
  }

  private scrolling(evt): void {
    evt.stopPropagation();
  }

  private cellMouseEnter(evt): void {
    evt.stopPropagation();
    let elem: any = evt.target;
    let count = 1;

    while ((elem = elem.nextSibling) && !/taken/.test(elem.className) && count < this.scheduler.duration) {
      count++;
      elem.className += ' hover'
    }

    if (count < this.scheduler.duration) {
      evt.target.parentElement.querySelectorAll('.hover').forEach((item: HTMLDivElement) => {
        item.className = item.className.replace(' hover', '')
      });
      evt.target.removeEventListener('click', this.cellClicked.bind(this));
    } else {
      evt.target.className += ' can-select';
      evt.target.addEventListener('click', this.cellClicked.bind(this));
    }
  }

  private cellClicked(evt) {
    evt.stopPropagation();
    window.location.href = this.scheduler.target + '?room=' + evt.target.getAttribute('data-room') + '&start=' + evt.target.getAttribute('data-date') + '&duration=' + this.scheduler.duration + '&adults=' + this.scheduler.adults + '&children=' + this.scheduler.children;
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
    this.header = document.createElement('div');
    this.header.className = 'header';
    this.panel.appendChild(this.header);
    this.scheduler.getRange().map((date: Date) => {
      const cell: HTMLElement = document.createElement('div');
      cell.className = 'cell day-week-' + date.getDay();
      cell.innerText = this.days[date.getDay()] + ' ' + date.getDate().toString() + '/' + (date.getMonth() + 1).toString();
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

  public render() {
    this.addClassName();
    this.renderAside();
    this.renderPanel();
    this.renderHeader();
    this.scheduler
      .load()
      .then((data: RatesApiResponseInterface) => {
        this.renderSlots(data)
      });
  }
}