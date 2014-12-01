# SQRT\Controller

Компонент содержит базовые классы контроллера, а также стандартные EventListener и ControllerResolver для HTTPKernel.

## Контроллер

Все создаваемые контроллеры должны наследовать базовый класс Controller. Каждый action контроллера должен вернуть 
результат для выдачи в браузер, это может быть:

* объект Response
* объект Template
* массив (будет преобразован в JsonResponse)
* значение для вывода (строка, число, и т.п.)

Базовый класс контроллера имеет набор методов для типовых действий:

* `getRequest()` - получить объект Request 
* `getUrl()` - получить текущий URL
* `getSession($autostart = true)` - получить объект Session. Сессия стартует при первом вызове, если она явно не была 
запущена ранее.
* `isAjax()` - проверка, что сделан Ajax-запрос. Альяс для `$this->getRequest()->isXmlHttpRequest()`

### Именование контроллеров

В системе установлены соглашения на именование контроллеров и действий (action). По-умолчанию, для главной страницы 
вызывается `defaultController->indexAction()`.

Если указан один аргумент `/some/`, при наличии контроллера `someController` в нем будет вызван `indexAction()`. 
Если такого контроллера нет, система попытается вызвать `defaultController->someAction()`

Для URL `/some/work/` будет предпринята попытка вызвать `someController->workAction()`.

Если по первому аргументу контроллер не будет найден, система попробует вызвать `defaultController->someAction()`. 
Если и это не получится, будет возвращена 404 ошибка.

Если контроллер найден, но такого метода не существует, система также выдаст ошибку. 

#### Динамические адреса

Если нужно предусмотреть возможность гибкой адресации, когда заранее неизвестно, какие адреса будут использоваться,
например для создания человекопонятных URL, в контроллере можно реализовать функцию `__call()`, внутри которой 
определять логику работы с данным адресом. Например, для контроллера `someController`:
 
    function __call()
    {
        $slug = $this->getUrl()->getArgument(2);
        // Далее идет логика обработки запроса 
    }
    
Таким образом для адресов `/some/thing/` и `/some/where/` переменная `$slug` будет содержать `'thing'` и `'where'` соответственно.

### Ошибки

* `notFound()` - выбрасывает `HttpException` с кодом 404
* `forbidden()` - выбрасывает `HttpException` c кодом 403
     
### Редирект

* `redirect($url, $status = null)` - возвращает объект RedirectResponse. По-умолчанию HTTP статус 302.
* `back()` - редирект на HTTP_REFERER. Если HTTP_REFERER не указан, возвращает на главную страницу. 

### Шаблонизатор

В системе используется шаблонизатор [Plates](http://platesphp.com), основанный на нативном синтаксисе PHP.

В контроллере предусмотрены методы для работы с шаблонами:

* `template($name, $data = null)` - Для создания объекта шаблона 
* `render($name, $data = null)` - Создание и рендер шаблона

При необходимости донастройки, можно получить объект Engine или полностью переопределить его:

* `getTemplatesEngine()` - получить объект Engine
* `setTemplatesEngine(Engine $engine)` - задать объект Engine