# IBLOCK_DIFF
Сравнение структуры инфоблоков битрикс

# Использование
1. Скрипт должен лежать в папке IBLOCK_DIFF в корне битрикса
2. В файле config.php указываем пути к двум битриксам. Можно указывать удаленные пути
```php
$bitrix1 = realpath(__DIR__ . '/..');
$bitrix2 = 'http://bitrixdev.ru';
```

3. Скрипт IBLOCK_DIFF так же должен быть на удаленном сайте
4. Запускаем index.php
5. Видим результат

# Установка
1. В корне битрикса прописать ``git clone https://github.com/MashinaMashina/IBLOCK_DIFF``
2. Изменить второй проект в файле IBLOCK_DIFF/config.php
