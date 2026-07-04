# Задача для агента Cursor: План — подчёркивание под блоком саб-меню машин

Контекст: подчёркивание должно быть под ВСЕМ блоком саб-меню машин (линия на всю
ширину), а не короткой чертой на активном табе. Сделали как DS-меню: `border-bottom`
1px на `.nav2` (линия под блоком), активный таб — розовый 2px-сегмент внизу; старый
`hr` скрыт; JS-якорь `.wrapper` переведён на `.nav2`. Правка в `plan/index.php`.
После заливки проверит Claude в браузере. Песочница `pr-diz-develop.ru`.

## Что залить (ровно этот 1 файл)

- `plan/index.php` → `/plan/index.php`

## Доступы

FTP-доступы в `.vscode/sftp.json` (host, username, password, port 21, ftp,
remotePath `/`). Пароль НЕ выводи в терминал и НЕ пиши в файлы.

## Как залить (терминал, из корня проекта)

```bash
cd "$(git rev-parse --show-toplevel 2>/dev/null || pwd)"

read -r HOST USER_ PASS < <(python3 - <<'PY'
import json
d = json.load(open('.vscode/sftp.json'))
print(d['host'], d['username'], d['password'])
PY
)

f=plan/index.php
if curl -sS --ftp-create-dirs -T "$f" "ftp://$HOST/$f" --user "$USER_:$PASS"; then
  echo "OK  → $f"
else
  echo "FAIL → $f"
fi
```

## Правила

- Заливай ТОЛЬКО этот файл. Ничего не удаляй, другое не трогай.
- Никаких git-команд — только FTP.
- Не печатай пароль в консоль.

## Проверка

```bash
curl -s -o /dev/null -w "%{http_code}  plan/\n" "https://pr-diz-develop.ru/plan/"
```

200. Дальше Claude сам сверит подчёркивание в браузере (скрин + замер).
