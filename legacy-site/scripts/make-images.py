"""Prepare the legacy site's images.

Re-saves the downloaded photos the way an unoptimized camera export looks
(maximum JPEG quality, no chroma subsampling, so each file is several MB) and
builds the hero banner with the event details baked into the pixels (B01).
"""
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont

ASSETS = Path(__file__).resolve().parent.parent / "assets"
FONTS = Path("C:/Windows/Fonts")

for i in range(1, 6):
    src = ASSETS / f"photo-{i}.jpg"
    Image.open(src).convert("RGB").save(
        ASSETS / f"carousel-{i}.jpg", quality=100, subsampling=0
    )

hero = Image.open(ASSETS / "photo-1.jpg").convert("RGB")
overlay = Image.new("RGBA", hero.size, (0, 0, 0, 0))
ImageDraw.Draw(overlay).rectangle([0, 900, hero.width, 1900], fill=(20, 60, 110, 170))
hero = Image.alpha_composite(hero.convert("RGBA"), overlay).convert("RGB")

draw = ImageDraw.Draw(hero)
big = ImageFont.truetype(str(FONTS / "georgiab.ttf"), 260)
small = ImageFont.truetype(str(FONTS / "segoeuib.ttf"), 130)
draw.text((250, 1000), "PUBLIC HEARING", font=big, fill="white")
draw.text((260, 1350), "Oct 30, 6 PM  \u2022  Riverbend Civic Center", font=small, fill="white")
draw.text((260, 1560), "Regional Transportation Plan 2050", font=small, fill=(255, 230, 150))
hero.save(ASSETS / "hero-public-hearing.jpg", quality=100, subsampling=0)

for f in sorted(ASSETS.glob("*.jpg")):
    print(f"{f.name}: {f.stat().st_size / 1_000_000:.1f} MB")
