// Neblina de la intro: la foto del hero como textura, cubierta por niebla animada (ruido fbm
// que deriva en horizontal). Sin librerías.
//
// uFog (1 → 0) lo mueve el scroll (resources/js/story.js): con 1 la foto apenas se adivina,
// desenfocada y algo más cerca; con 0 queda nítida y solo persiste una niebla baja que sigue
// moviéndose sobre el mar de nubes.
//
// La textura es la imagen que el navegador ya eligió (img.currentSrc), así respeta el
// <picture>: Illimani en escritorio, Huayna Potosí vertical en móvil, AVIF o WebP.

const VERT = `
attribute vec2 a;
void main() { gl_Position = vec4(a, 0.0, 1.0); }
`;

const FRAG = `
precision highp float;
uniform sampler2D uTex;
uniform vec2 uRes;
uniform vec2 uImg;
uniform float uTime;
uniform float uFog;

float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }

float noise(vec2 p) {
    vec2 i = floor(p), f = fract(p);
    vec2 u = f * f * (3.0 - 2.0 * f);
    return mix(mix(hash(i), hash(i + vec2(1.0, 0.0)), u.x),
               mix(hash(i + vec2(0.0, 1.0)), hash(i + vec2(1.0, 1.0)), u.x), u.y);
}

float fbm(vec2 p) {
    float v = 0.0, a = 0.5;
    mat2 m = mat2(1.6, 1.2, -1.2, 1.6);
    for (int i = 0; i < 5; i++) { v += a * noise(p); p = m * p; a *= 0.5; }
    return v;
}

void main() {
    vec2 uv = gl_FragCoord.xy / uRes;

    // object-fit: cover, con un leve acercamiento mientras hay niebla
    float sr = uRes.x / uRes.y, ir = uImg.x / uImg.y;
    vec2 scale = sr > ir ? vec2(1.0, ir / sr) : vec2(sr / ir, 1.0);
    float zoom = 1.0 + 0.08 * uFog;
    vec2 tuv = (uv - 0.5) * scale / zoom + 0.5;
    tuv.y = 1.0 - tuv.y;

    // Desenfoque: 9 muestras en dos anillos (cruz + diagonales), más abiertas cuanto más niebla.
    // Con menos muestras quedan bordes dobles en las crestas.
    float r = 0.01 * uFog;
    float d2 = r * 0.707;
    vec3 col = texture2D(uTex, tuv).rgb * 0.2;
    col += (texture2D(uTex, tuv + vec2( r, 0.0)).rgb + texture2D(uTex, tuv + vec2(-r, 0.0)).rgb
          + texture2D(uTex, tuv + vec2(0.0,  r)).rgb + texture2D(uTex, tuv + vec2(0.0, -r)).rgb) * 0.1;
    col += (texture2D(uTex, tuv + vec2( d2,  d2) * 0.5).rgb + texture2D(uTex, tuv + vec2(-d2,  d2) * 0.5).rgb
          + texture2D(uTex, tuv + vec2( d2, -d2) * 0.5).rgb + texture2D(uTex, tuv + vec2(-d2, -d2) * 0.5).rgb) * 0.1;

    // Dos capas de niebla que derivan a distinta velocidad
    float t = uTime;
    vec2 p = vec2(uv.x * sr, uv.y);
    float n1 = fbm(p * vec2(1.6, 3.2) + vec2(t * 0.035, t * 0.004));
    float n2 = fbm(p * vec2(3.0, 5.0) + vec2(-t * 0.06, 0.0) + 7.3);
    float n = mix(n1, n2, 0.35);

    // Niebla general (se va con el scroll) + banco bajo que siempre queda
    float wall = uFog * (0.55 + 0.75 * n);
    float bank = 0.38 * smoothstep(0.62, 0.05, uv.y) * smoothstep(0.25, 0.75, n);
    float d = clamp(wall + bank * (1.0 - 0.4 * uFog), 0.0, 0.94);

    vec3 fogCol = vec3(0.80, 0.78, 0.76) * (0.82 + 0.18 * n);
    col = mix(col, fogCol, d);

    col += (hash(gl_FragCoord.xy + uTime) - 0.5) / 160.0; // tramado contra el banding
    gl_FragColor = vec4(col, 1.0);
}
`;

export function initFog(canvas, img) {
    const gl = canvas.getContext('webgl', { antialias: false, alpha: false, powerPreference: 'low-power' });
    if (!gl) return null;

    const compile = (type, src) => {
        const s = gl.createShader(type);
        gl.shaderSource(s, src);
        gl.compileShader(s);
        return gl.getShaderParameter(s, gl.COMPILE_STATUS) ? s : null;
    };
    const vs = compile(gl.VERTEX_SHADER, VERT);
    const fs = compile(gl.FRAGMENT_SHADER, FRAG);
    if (!vs || !fs) return null;

    const prog = gl.createProgram();
    gl.attachShader(prog, vs);
    gl.attachShader(prog, fs);
    gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) return null;
    gl.useProgram(prog);

    gl.bindBuffer(gl.ARRAY_BUFFER, gl.createBuffer());
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    const loc = gl.getAttribLocation(prog, 'a');
    gl.enableVertexAttribArray(loc);
    gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);

    const u = name => gl.getUniformLocation(prog, name);
    const uRes = u('uRes'), uImg = u('uImg'), uTime = u('uTime'), uFog = u('uFog');

    // Textura sin mipmaps: vale para cualquier tamaño (WebGL 1 pide potencias de 2 para mipmaps)
    const tex = gl.createTexture();
    gl.bindTexture(gl.TEXTURE_2D, tex);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
    let hasImage = false;

    const upload = () => {
        if (!img.naturalWidth) return;
        gl.bindTexture(gl.TEXTURE_2D, tex);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, img);
        gl.uniform2f(uImg, img.naturalWidth, img.naturalHeight);
        hasImage = true;
        draw();
    };

    const SCALE = 0.75; // resolución de render respecto al tamaño en CSS
    const resize = () => {
        canvas.width = Math.max(1, Math.round(canvas.clientWidth * SCALE));
        canvas.height = Math.max(1, Math.round(canvas.clientHeight * SCALE));
        gl.viewport(0, 0, canvas.width, canvas.height);
        gl.uniform2f(uRes, canvas.width, canvas.height);
    };

    let fog = 1;
    let seconds = 0;
    const draw = () => {
        if (!hasImage) { // mientras carga la foto: niebla lisa, nunca la foto nítida
            gl.clearColor(0.78, 0.76, 0.74, 1);
            gl.clear(gl.COLOR_BUFFER_BIT);
            return;
        }
        gl.uniform1f(uTime, seconds);
        gl.uniform1f(uFog, fog);
        gl.drawArrays(gl.TRIANGLES, 0, 3);
    };

    const start = performance.now();
    let raf = 0;
    let onScreen = true;
    const loop = (now) => {
        seconds = (now - start) / 1000;
        draw();
        raf = requestAnimationFrame(loop);
    };
    const play = () => { if (!raf && onScreen && !document.hidden) raf = requestAnimationFrame(loop); };
    const pause = () => { cancelAnimationFrame(raf); raf = 0; };

    resize();
    draw();
    new ResizeObserver(() => { resize(); draw(); }).observe(canvas);
    new IntersectionObserver(([e]) => { onScreen = e.isIntersecting; onScreen ? play() : pause(); }).observe(canvas);
    document.addEventListener('visibilitychange', () => (document.hidden ? pause() : play()));

    // Si el navegador cambia de imagen (rotar el móvil cruza el breakpoint), se vuelve a subir
    img.addEventListener('load', upload);
    if (img.complete) upload();
    play();

    return {
        setFog(v) { fog = v; if (!raf) draw(); },
    };
}
