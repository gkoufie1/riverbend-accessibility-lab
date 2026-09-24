"""Crop the Atlanta night skyline photo into web-sized pieces for the design layer.

Source: "Jackson Street Bridge, Atlanta, United States" by Joey Kyber (Unsplash),
via Wikimedia Commons. License: CC0 1.0 (public domain dedication).
https://commons.wikimedia.org/wiki/File:Jackson_Street_Bridge,_Atlanta,_United_States_(Unsplash_vXtX07KVcE8).jpg
"""
from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "assets" / "atlanta-night-jackson-st.jpg"
OUT = ROOT / "mu-plugins" / "riverbend-design"

img = Image.open(SRC).convert("RGB")  # 3840 x 2560


def save(box, width, name):
    crop = img.crop(box)
    crop = crop.resize((width, round(crop.height * width / crop.width)), Image.LANCZOS)
    crop.save(OUT / name, quality=78, optimize=True, progressive=True)
    print(f"{name}: {crop.size[0]}x{crop.size[1]}, {(OUT / name).stat().st_size // 1024} KB")


save((0, 0, 1728, 2560), 900, "side-left.jpg")        # Truist Plaza side
save((2300, 0, 3840, 2560), 900, "side-right.jpg")     # right-hand towers
save((0, 60, 3840, 1500), 1920, "banner.jpg")          # skyline for title bands
save((0, 1400, 3840, 2560), 1920, "footer.jpg")        # highway light trails
