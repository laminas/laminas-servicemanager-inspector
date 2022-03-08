# laminas-servicemanager-inspector

[![Build Status](https://github.com/laminas/laminas-servicemanager-inspector/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/laminas/laminas-servicemanager-inspector/actions/workflows/continuous-integration.yml)

> ## 🇷🇺 Русским гражданам
> 
> Мы, участники Laminas, родились и живем в разных странах. У многих из нас есть друзья, родственники и коллеги как в России, так и в Украине. Некоторые из нас родились в России. Некоторые из нас живут в России. У некоторых бабушки и дедушки сражались с фашистами во Второй мировой войне. Здесь никто не поддерживает фашизм.
> 
> У одного из нас есть украинская родственница, которая спаслась из дома вместе с сыном. Поезд задержался из-за бомбежки на дороге впереди. У нас есть друзья, которые прячутся в бомбоубежищах. Мы с тревогой ждем весточки от них после воздушных налетов, которые беспорядочно наносят удары и попадают по больницам, школам, детским садам и домам. Мы не берем это из каких-либо СМИ. Мы наблюдаем это напрямую.
> 
> Вы доверяете нам достаточно, чтоб использовать наши программы, и мы просим вас довериться нам вновь. Мы нуждаемся в помощи. Выходите и протестуйте против этой бесполезной войны. Остановите кровопролитие. Скажите "Нет войне!"
> 
> ## 🇺🇸 To Citizens of Russia
> 
> We at Laminas come from all over the world. Many of us have friends, family and colleagues in both Russia and Ukraine. Some of us were born in Russia. Some of us currently live in Russia. Some have grandparents who fought Nazis in World War II. Nobody here supports fascism.
> 
> One team member has a Ukrainian relative who fled her home with her son. The train was delayed due to bombing on the road ahead. We have friends who are hiding in bomb shelters. We anxiously follow up on them after the air raids, which indiscriminately fire at hospitals, schools, kindergartens and houses. We're not taking this from any media. These are our actual experiences.
> 
> You trust us enough to use our software. We ask that you trust us to say the truth on this. We need your help. Go out and protest this unnecessary war. Stop the bloodshed. Say "stop the war!"
[![Psalm coverage](https://shepherd.dev/github/laminas/laminas-servicemanager-inspector/coverage.svg?)](https://shepherd.dev/github/laminas/laminas-servicemanager-inspector)

The purpose of this package is to make autowiring reliable (no `AoT` is involved).
At the moment it makes sure that `ReflectionBasedAbstractFactory` won't cause any runtime problems.

The tool can be added to your favorite `CI` so to make sure there are no defects.

[TSC Proposal](https://github.com/laminas/technical-steering-committee/issues/55)

## Defects analyzed

* Circular dependency
* Cyclic alias 
* Missing factory 
* Factory autoload failure
* Unresolvable service 
* Unresolvable scalar
* Scalar type/name mismatch (WIP)
* Other scenarios of misconfiguration

`Laminas/DI/AutowireFactory` support will be coming soon.

## Installation

Run the following to install this library:

```bash
$ composer require --dev laminas/laminas-servicemanager-inspector
```

## Configuration

No configuration is needed. All you need to do is to include `ConfigProvider` (or `Module`) to your application.

## Usage

```bash
./vendor/bin/laminas servicemanager:inspect
```

## Future plans

- Analyze `AutowireFactory` (from `laminas-di`)
- Check if each and every app's root entrypoint (e.g. a class implementing `RequestHandlerInterface`) has a proper factory

## Support

* [Issues](https://github.com/laminas/laminas-servicemanager-inspector/issues/)
* [Forum](https://discourse.laminas.dev/)
