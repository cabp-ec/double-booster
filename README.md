# Parallel Booster Framework
### _A coding challenge for Deft_

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=dev)](https://travis-ci.org/joemccann/dillinger)

For this approach I'm taking several dessign patterns into consideration,
implemented using PHP in order to create an escalable solution.

## The Framework

- PSR-compliant
- Zero 3rd party libraries
- OOP & Dessign Patterns

This framework intents to ease-up development while taking performance into consideration. I decided to solve the challenge this way because it demonstrate hard skills in a functional fashion. While building the bootstrapping meechanism for the framework, I found something I like: have you seen those rockets with 2 fuel roket-like fuel tanks...? Those are used for boosting a rocket into space after lift-of, during [Booster Staging][df2]
As [NASA] writes on their [Solar System Exploration site][df1]:

> Interplanetary mission operations may be considered in four phases:
> the Launch Phase, the Cruise Phase, the Encounter Phase, and,
> depending on the state of spacecraft health and mission funding,
> the Extended Operations Phase.

You can see a reflection of such stages in the bootstrapping mechanism, hence the framework name, by taking a look at files `public/index.php` and `src/Kernel.php`.

## Design Patterns

- Facade
- Chain Of Responsibilities (i.e. the most important one)
- Pool
- Dependency Injection

The approach taken implements PSR-compliant middleware using the Chain Of Responsibilities pattern in a slightly modified way. The default behaviour is "To build a chain of objects to handle a call in sequential order. If one object cannot handle a call, it delegates the call to the next in the chain and so forth."; in this case I'm using it the other way around: "If one object cannot handle a call, it breaks the execution chain". In this way we can generate multiple security layers before even reach-out for a controller.

## Routing

The routing mechanism simply looks for a matching route (without the use of RegEx) in the predefined sets. In this case I'm deliverately NOT using RESTful but RPC:

- API - a group for holding either RPC or RESTful routes, use the file `config/routes/api.php` for this group
- WEB - a group for holding routes that outpu HTTP content (e.g. web pages, JS, images), use the `config/routes/web.php` for this group

And of course, you can use parameters in a JSON body and/or as URL parameter segment, like so:

- /{version}
- /{version}/crm/customers
- /{version}/crm/customer/
- /{version}/crm/customer/{code}
- /{version}/crm/customer/{code}/profile

## Installation

```sh
cd webpub
composer install
```

For production environments...

```sh
cd webpub
composer install
```

## Development

Want to contribute? Great!

## License

MIT

**Free Software, Hell Yeah!**

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax)

[NASA]: <https://nasa.gov>
[df1]: <https://solarsystem.nasa.gov/basics/chapter14-1/#:~:text=Interplanetary%20mission%20operations%20may%20be,funding%2C%20the%20Extended%20Operations%20Phase.>
[df2]: <https://www.grc.nasa.gov/www/k-12/rocket/rktstage.html#:~:text=The%20first%20stage%20is%20ignited,the%20second%20stage%20into%20orbit.>
