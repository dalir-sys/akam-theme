import sys
from PIL import Image
out, H, scale = sys.argv[1], int(sys.argv[2]), int(sys.argv[3])
frames = [(f.rsplit(':', 1)[0], int(f.rsplit(':', 1)[1])) for f in sys.argv[4:]]
first = Image.open(frames[0][0]); W = first.size[0]
canvas = Image.new('RGB', (W, H * scale), 'white')
for f, top in frames:
    canvas.paste(Image.open(f).convert('RGB'), (0, top * scale))
canvas.save(out, optimize=True)
