"""Tiny Elementor JSON builder."""
import json, random, string

def _id():
    return ''.join(random.choice('0123456789abcdef') for _ in range(7))

def w(widget_type, **settings):
    return {"id": _id(), "elType": "widget", "widgetType": widget_type, "settings": settings, "elements": [], "isInner": False}

def c(*children, inner=False, **settings):
    s = {"content_width": "boxed"} if not inner else {"content_width": "full"}
    s.update(settings)
    return {"id": _id(), "elType": "container", "settings": s, "elements": list(children), "isInner": inner}

def row(*children, gap=24, **settings):
    s = dict(flex_direction="row", flex_gap={"column": str(gap), "row": str(gap), "isLinked": True, "unit": "px", "size": gap})
    s.update(settings)
    return c(*children, inner=True, **s)

def col(*children, width=None, **settings):
    s = dict(flex_direction="column")
    if width is not None:
        s.update(width={"unit": "%", "size": width, "sizes": []}, width_mobile={"unit": "%", "size": 100, "sizes": []})
    s.update(settings)
    return c(*children, inner=True, **s)

def url(u="#"):
    return {"url": u, "is_external": "", "nofollow": "", "custom_attributes": ""}

def img(att_id, src):
    return {"url": src, "id": att_id, "size": "", "alt": "", "source": "library"}

def icon(value, lib="fa-solid"):
    return {"value": value, "library": lib}

def pad(t, r=None, b=None, l=None, unit="px"):
    r = t if r is None else r; b = t if b is None else b; l = r if l is None else l
    return {"unit": unit, "top": str(t), "right": str(r), "bottom": str(b), "left": str(l), "isLinked": False}

def dump(tree, path):
    with open(path, "w") as f:
        json.dump(tree, f, ensure_ascii=False)
