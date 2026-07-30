var aN = Object.defineProperty;
var by = (le) => {
  throw TypeError(le);
};
var oN = (le, de, Le) =>
  de in le
    ? aN(le, de, { enumerable: !0, configurable: !0, writable: !0, value: Le })
    : (le[de] = Le);
var qn = (le, de, Le) => oN(le, typeof de != "symbol" ? de + "" : de, Le),
  id = (le, de, Le) => de.has(le) || by("Cannot " + Le);
var h = (le, de, Le) => (
    id(le, de, "read from private field"),
    Le ? Le.call(le) : de.get(le)
  ),
  $ = (le, de, Le) =>
    de.has(le)
      ? by("Cannot add the same private member more than once")
      : de instanceof WeakSet
        ? de.add(le)
        : de.set(le, Le),
  I = (le, de, Le, qs) => (
    id(le, de, "write to private field"),
    qs ? qs.call(le, Le) : de.set(le, Le),
    Le
  ),
  G = (le, de, Le) => (id(le, de, "access private method"), Le);
var Vi = (le, de, Le, qs) => ({
  set _(Te) {
    I(le, de, Te, Le);
  },
  get _() {
    return h(le, de, qs);
  },
});
(function () {
  "use strict";
  var Pn,
    Jr,
    Ps,
    cy,
    Xr,
    ad,
    uy,
    Os,
    en,
    Is,
    dy,
    On,
    py,
    In,
    Ls,
    Lt,
    Ln,
    Be,
    qa,
    Dn,
    St,
    wy,
    Sr,
    my,
    ft,
    re,
    Va,
    it,
    zn,
    Ds,
    xr,
    tn,
    Ka,
    zs,
    Ms,
    Mn,
    $n,
    rn,
    $s,
    ie,
    Ja,
    ld,
    cd,
    ud,
    dd,
    pd,
    md,
    fd,
    ky,
    fy,
    Wa,
    er,
    Xe,
    Fn,
    tr,
    cn,
    hy,
    br,
    qt,
    Ha,
    gy,
    wr,
    nn,
    ht,
    kr,
    _r,
    Ki,
    hd,
    yy,
    rr,
    vy,
    Ee,
    sn,
    an,
    Fs,
    Us,
    on,
    Zs,
    Bs,
    xy,
    ln,
    Un,
    Vt,
    Qa,
    Ga,
    Zi;
  var le = document.createElement("style");
  ((le.textContent = `*,:before,:after{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }*,:before,:after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}:before,:after{--tw-content: ""}html,:host{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dl,dd,h1,h2,h3,h4,h5,h6,hr,figure,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}ol,ul,menu{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}button,[role=button]{cursor:pointer}:disabled{cursor:default}img,svg,video,canvas,audio,iframe,embed,object{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}.visible{visibility:visible}.static{position:static}.fixed{position:fixed}.absolute{position:absolute}.relative{position:relative}.inset-0{inset:0}.bottom-0{bottom:0}.bottom-4{bottom:1rem}.right-4{right:1rem}.top-0{top:0}.top-0\\.5{top:.125rem}.top-1\\/2{top:50%}.top-4{top:1rem}.top-\\[-3px\\]{top:-3px}.z-50{z-index:50}.mb-0{margin-bottom:0}.mb-1{margin-bottom:.25rem}.mb-2{margin-bottom:.5rem}.mb-3{margin-bottom:.75rem}.mb-4{margin-bottom:1rem}.mb-5{margin-bottom:1.25rem}.mb-6{margin-bottom:1.5rem}.mb-8{margin-bottom:2rem}.ml-2{margin-left:.5rem}.ml-4{margin-left:1rem}.mt-0\\.5{margin-top:.125rem}.mt-1{margin-top:.25rem}.mt-2{margin-top:.5rem}.mt-3{margin-top:.75rem}.mt-4{margin-top:1rem}.mt-6{margin-top:1.5rem}.block{display:block}.inline-block{display:inline-block}.inline{display:inline}.flex{display:flex}.inline-flex{display:inline-flex}.table{display:table}.grid{display:grid}.hidden{display:none}.h-10{height:2.5rem}.h-12{height:3rem}.h-16{height:4rem}.h-2{height:.5rem}.h-2\\.5{height:.625rem}.h-24{height:6rem}.h-28{height:7rem}.h-3{height:.75rem}.h-4{height:1rem}.h-40{height:10rem}.h-5{height:1.25rem}.h-6{height:1.5rem}.h-7{height:1.75rem}.h-8{height:2rem}.h-full{height:100%}.max-h-48{max-height:12rem}.w-0\\.5{width:.125rem}.w-11{width:2.75rem}.w-12{width:3rem}.w-2{width:.5rem}.w-24{width:6rem}.w-36{width:9rem}.w-4{width:1rem}.w-40{width:10rem}.w-5{width:1.25rem}.w-7{width:1.75rem}.w-80{width:20rem}.w-full{width:100%}.min-w-0{min-width:0px}.min-w-\\[52px\\]{min-width:52px}.min-w-full{min-width:100%}.max-w-2xl{max-width:42rem}.max-w-3xl{max-width:48rem}.max-w-4xl{max-width:56rem}.max-w-5xl{max-width:64rem}.max-w-md{max-width:28rem}.flex-1{flex:1 1 0%}.flex-shrink-0,.shrink-0{flex-shrink:0}.-translate-y-1\\/2{--tw-translate-y: -50%;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.transform{transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}@keyframes pulse{50%{opacity:.5}}.animate-pulse{animation:pulse 2s cubic-bezier(.4,0,.6,1) infinite}.cursor-pointer{cursor:pointer}.resize{resize:both}.flex-col{flex-direction:column}.flex-wrap{flex-wrap:wrap}.items-start{align-items:flex-start}.items-center{align-items:center}.justify-end{justify-content:flex-end}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-1\\.5{gap:.375rem}.gap-2{gap:.5rem}.gap-3{gap:.75rem}.gap-4{gap:1rem}.gap-5{gap:1.25rem}.gap-6{gap:1.5rem}.space-y-2>:not([hidden])~:not([hidden]){--tw-space-y-reverse: 0;margin-top:calc(.5rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(.5rem * var(--tw-space-y-reverse))}.space-y-3>:not([hidden])~:not([hidden]){--tw-space-y-reverse: 0;margin-top:calc(.75rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(.75rem * var(--tw-space-y-reverse))}.space-y-4>:not([hidden])~:not([hidden]){--tw-space-y-reverse: 0;margin-top:calc(1rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1rem * var(--tw-space-y-reverse))}.space-y-6>:not([hidden])~:not([hidden]){--tw-space-y-reverse: 0;margin-top:calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem * var(--tw-space-y-reverse))}.divide-y>:not([hidden])~:not([hidden]){--tw-divide-y-reverse: 0;border-top-width:calc(1px * calc(1 - var(--tw-divide-y-reverse)));border-bottom-width:calc(1px * var(--tw-divide-y-reverse))}.divide-gray-100>:not([hidden])~:not([hidden]){--tw-divide-opacity: 1;border-color:rgb(243 244 246 / var(--tw-divide-opacity, 1))}.divide-gray-200>:not([hidden])~:not([hidden]){--tw-divide-opacity: 1;border-color:rgb(229 231 235 / var(--tw-divide-opacity, 1))}.overflow-auto{overflow:auto}.overflow-hidden{overflow:hidden}.overflow-x-auto{overflow-x:auto}.overflow-y-auto{overflow-y:auto}.truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.break-all{word-break:break-all}.rounded{border-radius:.25rem}.rounded-full{border-radius:9999px}.rounded-lg{border-radius:.5rem}.rounded-md{border-radius:.375rem}.border{border-width:1px}.border-2{border-width:2px}.border-b{border-bottom-width:1px}.border-b-2{border-bottom-width:2px}.border-t{border-top-width:1px}.border-dashed{border-style:dashed}.border-none{border-style:none}.border-amber-100{--tw-border-opacity: 1;border-color:rgb(254 243 199 / var(--tw-border-opacity, 1))}.border-amber-200{--tw-border-opacity: 1;border-color:rgb(253 230 138 / var(--tw-border-opacity, 1))}.border-blue-100{--tw-border-opacity: 1;border-color:rgb(219 234 254 / var(--tw-border-opacity, 1))}.border-blue-200{--tw-border-opacity: 1;border-color:rgb(191 219 254 / var(--tw-border-opacity, 1))}.border-blue-500{--tw-border-opacity: 1;border-color:rgb(59 130 246 / var(--tw-border-opacity, 1))}.border-blue-600{--tw-border-opacity: 1;border-color:rgb(37 99 235 / var(--tw-border-opacity, 1))}.border-gray-100{--tw-border-opacity: 1;border-color:rgb(243 244 246 / var(--tw-border-opacity, 1))}.border-gray-200{--tw-border-opacity: 1;border-color:rgb(229 231 235 / var(--tw-border-opacity, 1))}.border-gray-300{--tw-border-opacity: 1;border-color:rgb(209 213 219 / var(--tw-border-opacity, 1))}.border-gray-50{--tw-border-opacity: 1;border-color:rgb(249 250 251 / var(--tw-border-opacity, 1))}.border-green-100{--tw-border-opacity: 1;border-color:rgb(220 252 231 / var(--tw-border-opacity, 1))}.border-green-200{--tw-border-opacity: 1;border-color:rgb(187 247 208 / var(--tw-border-opacity, 1))}.border-green-300{--tw-border-opacity: 1;border-color:rgb(134 239 172 / var(--tw-border-opacity, 1))}.border-red-200{--tw-border-opacity: 1;border-color:rgb(254 202 202 / var(--tw-border-opacity, 1))}.border-red-300{--tw-border-opacity: 1;border-color:rgb(252 165 165 / var(--tw-border-opacity, 1))}.border-yellow-200{--tw-border-opacity: 1;border-color:rgb(254 240 138 / var(--tw-border-opacity, 1))}.border-yellow-300{--tw-border-opacity: 1;border-color:rgb(253 224 71 / var(--tw-border-opacity, 1))}.bg-amber-50{--tw-bg-opacity: 1;background-color:rgb(255 251 235 / var(--tw-bg-opacity, 1))}.bg-black\\/30{background-color:#0000004d}.bg-blue-100{--tw-bg-opacity: 1;background-color:rgb(219 234 254 / var(--tw-bg-opacity, 1))}.bg-blue-50{--tw-bg-opacity: 1;background-color:rgb(239 246 255 / var(--tw-bg-opacity, 1))}.bg-blue-600{--tw-bg-opacity: 1;background-color:rgb(37 99 235 / var(--tw-bg-opacity, 1))}.bg-gray-100{--tw-bg-opacity: 1;background-color:rgb(243 244 246 / var(--tw-bg-opacity, 1))}.bg-gray-200{--tw-bg-opacity: 1;background-color:rgb(229 231 235 / var(--tw-bg-opacity, 1))}.bg-gray-400{--tw-bg-opacity: 1;background-color:rgb(156 163 175 / var(--tw-bg-opacity, 1))}.bg-gray-50{--tw-bg-opacity: 1;background-color:rgb(249 250 251 / var(--tw-bg-opacity, 1))}.bg-green-100{--tw-bg-opacity: 1;background-color:rgb(220 252 231 / var(--tw-bg-opacity, 1))}.bg-green-50{--tw-bg-opacity: 1;background-color:rgb(240 253 244 / var(--tw-bg-opacity, 1))}.bg-green-500{--tw-bg-opacity: 1;background-color:rgb(34 197 94 / var(--tw-bg-opacity, 1))}.bg-green-600{--tw-bg-opacity: 1;background-color:rgb(22 163 74 / var(--tw-bg-opacity, 1))}.bg-green-700{--tw-bg-opacity: 1;background-color:rgb(21 128 61 / var(--tw-bg-opacity, 1))}.bg-orange-100{--tw-bg-opacity: 1;background-color:rgb(255 237 213 / var(--tw-bg-opacity, 1))}.bg-orange-50{--tw-bg-opacity: 1;background-color:rgb(255 247 237 / var(--tw-bg-opacity, 1))}.bg-purple-100{--tw-bg-opacity: 1;background-color:rgb(243 232 255 / var(--tw-bg-opacity, 1))}.bg-purple-50{--tw-bg-opacity: 1;background-color:rgb(250 245 255 / var(--tw-bg-opacity, 1))}.bg-red-100{--tw-bg-opacity: 1;background-color:rgb(254 226 226 / var(--tw-bg-opacity, 1))}.bg-red-50{--tw-bg-opacity: 1;background-color:rgb(254 242 242 / var(--tw-bg-opacity, 1))}.bg-red-500{--tw-bg-opacity: 1;background-color:rgb(239 68 68 / var(--tw-bg-opacity, 1))}.bg-red-600{--tw-bg-opacity: 1;background-color:rgb(220 38 38 / var(--tw-bg-opacity, 1))}.bg-teal-100{--tw-bg-opacity: 1;background-color:rgb(204 251 241 / var(--tw-bg-opacity, 1))}.bg-white{--tw-bg-opacity: 1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.bg-yellow-100{--tw-bg-opacity: 1;background-color:rgb(254 249 195 / var(--tw-bg-opacity, 1))}.bg-yellow-50{--tw-bg-opacity: 1;background-color:rgb(254 252 232 / var(--tw-bg-opacity, 1))}.p-1{padding:.25rem}.p-3{padding:.75rem}.p-4{padding:1rem}.p-5{padding:1.25rem}.p-6{padding:1.5rem}.px-2{padding-left:.5rem;padding-right:.5rem}.px-2\\.5{padding-left:.625rem;padding-right:.625rem}.px-3{padding-left:.75rem;padding-right:.75rem}.px-4{padding-left:1rem;padding-right:1rem}.px-5{padding-left:1.25rem;padding-right:1.25rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-0\\.5{padding-top:.125rem;padding-bottom:.125rem}.py-1{padding-top:.25rem;padding-bottom:.25rem}.py-1\\.5{padding-top:.375rem;padding-bottom:.375rem}.py-2{padding-top:.5rem;padding-bottom:.5rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-4{padding-top:1rem;padding-bottom:1rem}.py-6{padding-top:1.5rem;padding-bottom:1.5rem}.py-8{padding-top:2rem;padding-bottom:2rem}.pb-1{padding-bottom:.25rem}.pb-2{padding-bottom:.5rem}.pb-3{padding-bottom:.75rem}.pb-5{padding-bottom:1.25rem}.pr-4{padding-right:1rem}.pt-2{padding-top:.5rem}.pt-3{padding-top:.75rem}.pt-4{padding-top:1rem}.pt-6{padding-top:1.5rem}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.font-mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace}.text-2xl{font-size:1.5rem;line-height:2rem}.text-3xl{font-size:1.875rem;line-height:2.25rem}.text-base{font-size:1rem;line-height:1.5rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-sm{font-size:.875rem;line-height:1.25rem}.text-xl{font-size:1.25rem;line-height:1.75rem}.text-xs{font-size:.75rem;line-height:1rem}.font-bold{font-weight:700}.font-extrabold{font-weight:800}.font-medium{font-weight:500}.font-normal{font-weight:400}.font-semibold{font-weight:600}.uppercase{text-transform:uppercase}.lowercase{text-transform:lowercase}.leading-none{line-height:1}.leading-relaxed{line-height:1.625}.tracking-wide{letter-spacing:.025em}.tracking-wider{letter-spacing:.05em}.text-amber-800{--tw-text-opacity: 1;color:rgb(146 64 14 / var(--tw-text-opacity, 1))}.text-blue-600{--tw-text-opacity: 1;color:rgb(37 99 235 / var(--tw-text-opacity, 1))}.text-blue-700{--tw-text-opacity: 1;color:rgb(29 78 216 / var(--tw-text-opacity, 1))}.text-blue-800{--tw-text-opacity: 1;color:rgb(30 64 175 / var(--tw-text-opacity, 1))}.text-blue-900{--tw-text-opacity: 1;color:rgb(30 58 138 / var(--tw-text-opacity, 1))}.text-emerald-600{--tw-text-opacity: 1;color:rgb(5 150 105 / var(--tw-text-opacity, 1))}.text-gray-400{--tw-text-opacity: 1;color:rgb(156 163 175 / var(--tw-text-opacity, 1))}.text-gray-500{--tw-text-opacity: 1;color:rgb(107 114 128 / var(--tw-text-opacity, 1))}.text-gray-600{--tw-text-opacity: 1;color:rgb(75 85 99 / var(--tw-text-opacity, 1))}.text-gray-700{--tw-text-opacity: 1;color:rgb(55 65 81 / var(--tw-text-opacity, 1))}.text-gray-800{--tw-text-opacity: 1;color:rgb(31 41 55 / var(--tw-text-opacity, 1))}.text-gray-900{--tw-text-opacity: 1;color:rgb(17 24 39 / var(--tw-text-opacity, 1))}.text-green-700{--tw-text-opacity: 1;color:rgb(21 128 61 / var(--tw-text-opacity, 1))}.text-green-800{--tw-text-opacity: 1;color:rgb(22 101 52 / var(--tw-text-opacity, 1))}.text-indigo-600{--tw-text-opacity: 1;color:rgb(79 70 229 / var(--tw-text-opacity, 1))}.text-orange-700{--tw-text-opacity: 1;color:rgb(194 65 12 / var(--tw-text-opacity, 1))}.text-purple-700{--tw-text-opacity: 1;color:rgb(126 34 206 / var(--tw-text-opacity, 1))}.text-red-600{--tw-text-opacity: 1;color:rgb(220 38 38 / var(--tw-text-opacity, 1))}.text-red-700{--tw-text-opacity: 1;color:rgb(185 28 28 / var(--tw-text-opacity, 1))}.text-red-800{--tw-text-opacity: 1;color:rgb(153 27 27 / var(--tw-text-opacity, 1))}.text-teal-700{--tw-text-opacity: 1;color:rgb(15 118 110 / var(--tw-text-opacity, 1))}.text-white{--tw-text-opacity: 1;color:rgb(255 255 255 / var(--tw-text-opacity, 1))}.text-yellow-600{--tw-text-opacity: 1;color:rgb(202 138 4 / var(--tw-text-opacity, 1))}.text-yellow-700{--tw-text-opacity: 1;color:rgb(161 98 7 / var(--tw-text-opacity, 1))}.text-yellow-800{--tw-text-opacity: 1;color:rgb(133 77 14 / var(--tw-text-opacity, 1))}.underline{text-decoration-line:underline}.line-through{text-decoration-line:line-through}.opacity-50{opacity:.5}.opacity-55{opacity:.55}.opacity-70{opacity:.7}.opacity-80{opacity:.8}.shadow{--tw-shadow: 0 1px 3px 0 rgb(0 0 0 / .1), 0 1px 2px -1px rgb(0 0 0 / .1);--tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)}.shadow-lg{--tw-shadow: 0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);--tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)}.shadow-md{--tw-shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);--tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)}.shadow-sm{--tw-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);--tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)}.shadow-xl{--tw-shadow: 0 20px 25px -5px rgb(0 0 0 / .1), 0 8px 10px -6px rgb(0 0 0 / .1);--tw-shadow-colored: 0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)}.filter{filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.transition-colors{transition-property:color,background-color,border-color,text-decoration-color,fill,stroke;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.backdrop\\:bg-black\\/40::backdrop{background-color:#0006}.last\\:mb-0:last-child{margin-bottom:0}.last\\:border-0:last-child{border-width:0px}.last\\:pb-0:last-child{padding-bottom:0}.hover\\:bg-blue-50:hover{--tw-bg-opacity: 1;background-color:rgb(239 246 255 / var(--tw-bg-opacity, 1))}.hover\\:bg-blue-500:hover{--tw-bg-opacity: 1;background-color:rgb(59 130 246 / var(--tw-bg-opacity, 1))}.hover\\:bg-blue-700:hover{--tw-bg-opacity: 1;background-color:rgb(29 78 216 / var(--tw-bg-opacity, 1))}.hover\\:bg-gray-100:hover{--tw-bg-opacity: 1;background-color:rgb(243 244 246 / var(--tw-bg-opacity, 1))}.hover\\:bg-gray-200:hover{--tw-bg-opacity: 1;background-color:rgb(229 231 235 / var(--tw-bg-opacity, 1))}.hover\\:bg-gray-50:hover{--tw-bg-opacity: 1;background-color:rgb(249 250 251 / var(--tw-bg-opacity, 1))}.hover\\:bg-green-100:hover{--tw-bg-opacity: 1;background-color:rgb(220 252 231 / var(--tw-bg-opacity, 1))}.hover\\:bg-green-50:hover{--tw-bg-opacity: 1;background-color:rgb(240 253 244 / var(--tw-bg-opacity, 1))}.hover\\:bg-green-800:hover{--tw-bg-opacity: 1;background-color:rgb(22 101 52 / var(--tw-bg-opacity, 1))}.hover\\:bg-red-100:hover{--tw-bg-opacity: 1;background-color:rgb(254 226 226 / var(--tw-bg-opacity, 1))}.hover\\:bg-red-50:hover{--tw-bg-opacity: 1;background-color:rgb(254 242 242 / var(--tw-bg-opacity, 1))}.hover\\:bg-red-700:hover{--tw-bg-opacity: 1;background-color:rgb(185 28 28 / var(--tw-bg-opacity, 1))}.hover\\:bg-yellow-100:hover{--tw-bg-opacity: 1;background-color:rgb(254 249 195 / var(--tw-bg-opacity, 1))}.hover\\:text-blue-800:hover{--tw-text-opacity: 1;color:rgb(30 64 175 / var(--tw-text-opacity, 1))}.hover\\:text-gray-600:hover{--tw-text-opacity: 1;color:rgb(75 85 99 / var(--tw-text-opacity, 1))}.hover\\:text-gray-700:hover{--tw-text-opacity: 1;color:rgb(55 65 81 / var(--tw-text-opacity, 1))}.hover\\:text-indigo-800:hover{--tw-text-opacity: 1;color:rgb(55 48 163 / var(--tw-text-opacity, 1))}.hover\\:underline:hover{text-decoration-line:underline}.hover\\:opacity-100:hover{opacity:1}.focus\\:border-blue-500:focus{--tw-border-opacity: 1;border-color:rgb(59 130 246 / var(--tw-border-opacity, 1))}.focus\\:outline-none:focus{outline:2px solid transparent;outline-offset:2px}.focus\\:ring-1:focus{--tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow, 0 0 #0000)}.focus\\:ring-2:focus{--tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow, 0 0 #0000)}.focus\\:ring-inset:focus{--tw-ring-inset: inset}.focus\\:ring-blue-500:focus{--tw-ring-opacity: 1;--tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity, 1))}.focus\\:ring-offset-2:focus{--tw-ring-offset-width: 2px}.disabled\\:cursor-not-allowed:disabled{cursor:not-allowed}.disabled\\:opacity-50:disabled{opacity:.5}.disabled\\:opacity-60:disabled{opacity:.6}@media(min-width:640px){.sm\\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}
/*$vite$:1*/`),
    document.head.appendChild(le));
  const de = "modulepreload",
    Le = function (e) {
      return "/" + e;
    },
    qs = {},
    Te = function (t, r, n) {
      let s = Promise.resolve();
      function a(o) {
        const l = new Event("vite:preloadError", { cancelable: !0 });
        if (((l.payload = o), window.dispatchEvent(l), !l.defaultPrevented))
          throw o;
      }
      return s.then((o) => {
        for (const l of o || []) l.status === "rejected" && a(l.reason);
        return t().catch(a);
      });
    };
  function _y(e) {
    return e &&
      e.__esModule &&
      Object.prototype.hasOwnProperty.call(e, "default")
      ? e.default
      : e;
  }
  var xd = { exports: {} },
    eo = {},
    bd = { exports: {} },
    J = {};
  /**
   * @license React
   * react.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */ var Vs = Symbol.for("react.element"),
    Sy = Symbol.for("react.portal"),
    Cy = Symbol.for("react.fragment"),
    jy = Symbol.for("react.strict_mode"),
    Ny = Symbol.for("react.profiler"),
    Ry = Symbol.for("react.provider"),
    Ay = Symbol.for("react.context"),
    Ey = Symbol.for("react.forward_ref"),
    Ty = Symbol.for("react.suspense"),
    Py = Symbol.for("react.memo"),
    Oy = Symbol.for("react.lazy"),
    wd = Symbol.iterator;
  function Iy(e) {
    return e === null || typeof e != "object"
      ? null
      : ((e = (wd && e[wd]) || e["@@iterator"]),
        typeof e == "function" ? e : null);
  }
  var kd = {
      isMounted: function () {
        return !1;
      },
      enqueueForceUpdate: function () {},
      enqueueReplaceState: function () {},
      enqueueSetState: function () {},
    },
    _d = Object.assign,
    Sd = {};
  function Vn(e, t, r) {
    ((this.props = e),
      (this.context = t),
      (this.refs = Sd),
      (this.updater = r || kd));
  }
  ((Vn.prototype.isReactComponent = {}),
    (Vn.prototype.setState = function (e, t) {
      if (typeof e != "object" && typeof e != "function" && e != null)
        throw Error(
          "setState(...): takes an object of state variables to update or a function which returns an object of state variables."
        );
      this.updater.enqueueSetState(this, e, t, "setState");
    }),
    (Vn.prototype.forceUpdate = function (e) {
      this.updater.enqueueForceUpdate(this, e, "forceUpdate");
    }));
  function Cd() {}
  Cd.prototype = Vn.prototype;
  function Hi(e, t, r) {
    ((this.props = e),
      (this.context = t),
      (this.refs = Sd),
      (this.updater = r || kd));
  }
  var Qi = (Hi.prototype = new Cd());
  ((Qi.constructor = Hi), _d(Qi, Vn.prototype), (Qi.isPureReactComponent = !0));
  var jd = Array.isArray,
    Nd = Object.prototype.hasOwnProperty,
    Gi = { current: null },
    Rd = { key: !0, ref: !0, __self: !0, __source: !0 };
  function Ad(e, t, r) {
    var n,
      s = {},
      a = null,
      o = null;
    if (t != null)
      for (n in (t.ref !== void 0 && (o = t.ref),
      t.key !== void 0 && (a = "" + t.key),
      t))
        Nd.call(t, n) && !Rd.hasOwnProperty(n) && (s[n] = t[n]);
    var l = arguments.length - 2;
    if (l === 1) s.children = r;
    else if (1 < l) {
      for (var c = Array(l), u = 0; u < l; u++) c[u] = arguments[u + 2];
      s.children = c;
    }
    if (e && e.defaultProps)
      for (n in ((l = e.defaultProps), l)) s[n] === void 0 && (s[n] = l[n]);
    return {
      $$typeof: Vs,
      type: e,
      key: a,
      ref: o,
      props: s,
      _owner: Gi.current,
    };
  }
  function Ly(e, t) {
    return {
      $$typeof: Vs,
      type: e.type,
      key: t,
      ref: e.ref,
      props: e.props,
      _owner: e._owner,
    };
  }
  function Yi(e) {
    return typeof e == "object" && e !== null && e.$$typeof === Vs;
  }
  function Dy(e) {
    var t = { "=": "=0", ":": "=2" };
    return (
      "$" +
      e.replace(/[=:]/g, function (r) {
        return t[r];
      })
    );
  }
  var Ed = /\/+/g;
  function Ji(e, t) {
    return typeof e == "object" && e !== null && e.key != null
      ? Dy("" + e.key)
      : t.toString(36);
  }
  function to(e, t, r, n, s) {
    var a = typeof e;
    (a === "undefined" || a === "boolean") && (e = null);
    var o = !1;
    if (e === null) o = !0;
    else
      switch (a) {
        case "string":
        case "number":
          o = !0;
          break;
        case "object":
          switch (e.$$typeof) {
            case Vs:
            case Sy:
              o = !0;
          }
      }
    if (o)
      return (
        (o = e),
        (s = s(o)),
        (e = n === "" ? "." + Ji(o, 0) : n),
        jd(s)
          ? ((r = ""),
            e != null && (r = e.replace(Ed, "$&/") + "/"),
            to(s, t, r, "", function (u) {
              return u;
            }))
          : s != null &&
            (Yi(s) &&
              (s = Ly(
                s,
                r +
                  (!s.key || (o && o.key === s.key)
                    ? ""
                    : ("" + s.key).replace(Ed, "$&/") + "/") +
                  e
              )),
            t.push(s)),
        1
      );
    if (((o = 0), (n = n === "" ? "." : n + ":"), jd(e)))
      for (var l = 0; l < e.length; l++) {
        a = e[l];
        var c = n + Ji(a, l);
        o += to(a, t, r, c, s);
      }
    else if (((c = Iy(e)), typeof c == "function"))
      for (e = c.call(e), l = 0; !(a = e.next()).done; )
        ((a = a.value), (c = n + Ji(a, l++)), (o += to(a, t, r, c, s)));
    else if (a === "object")
      throw (
        (t = String(e)),
        Error(
          "Objects are not valid as a React child (found: " +
            (t === "[object Object]"
              ? "object with keys {" + Object.keys(e).join(", ") + "}"
              : t) +
            "). If you meant to render a collection of children, use an array instead."
        )
      );
    return o;
  }
  function ro(e, t, r) {
    if (e == null) return e;
    var n = [],
      s = 0;
    return (
      to(e, n, "", "", function (a) {
        return t.call(r, a, s++);
      }),
      n
    );
  }
  function zy(e) {
    if (e._status === -1) {
      var t = e._result;
      ((t = t()),
        t.then(
          function (r) {
            (e._status === 0 || e._status === -1) &&
              ((e._status = 1), (e._result = r));
          },
          function (r) {
            (e._status === 0 || e._status === -1) &&
              ((e._status = 2), (e._result = r));
          }
        ),
        e._status === -1 && ((e._status = 0), (e._result = t)));
    }
    if (e._status === 1) return e._result.default;
    throw e._result;
  }
  var et = { current: null },
    no = { transition: null },
    My = {
      ReactCurrentDispatcher: et,
      ReactCurrentBatchConfig: no,
      ReactCurrentOwner: Gi,
    };
  function Td() {
    throw Error("act(...) is not supported in production builds of React.");
  }
  ((J.Children = {
    map: ro,
    forEach: function (e, t, r) {
      ro(
        e,
        function () {
          t.apply(this, arguments);
        },
        r
      );
    },
    count: function (e) {
      var t = 0;
      return (
        ro(e, function () {
          t++;
        }),
        t
      );
    },
    toArray: function (e) {
      return (
        ro(e, function (t) {
          return t;
        }) || []
      );
    },
    only: function (e) {
      if (!Yi(e))
        throw Error(
          "React.Children.only expected to receive a single React element child."
        );
      return e;
    },
  }),
    (J.Component = Vn),
    (J.Fragment = Cy),
    (J.Profiler = Ny),
    (J.PureComponent = Hi),
    (J.StrictMode = jy),
    (J.Suspense = Ty),
    (J.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED = My),
    (J.act = Td),
    (J.cloneElement = function (e, t, r) {
      if (e == null)
        throw Error(
          "React.cloneElement(...): The argument must be a React element, but you passed " +
            e +
            "."
        );
      var n = _d({}, e.props),
        s = e.key,
        a = e.ref,
        o = e._owner;
      if (t != null) {
        if (
          (t.ref !== void 0 && ((a = t.ref), (o = Gi.current)),
          t.key !== void 0 && (s = "" + t.key),
          e.type && e.type.defaultProps)
        )
          var l = e.type.defaultProps;
        for (c in t)
          Nd.call(t, c) &&
            !Rd.hasOwnProperty(c) &&
            (n[c] = t[c] === void 0 && l !== void 0 ? l[c] : t[c]);
      }
      var c = arguments.length - 2;
      if (c === 1) n.children = r;
      else if (1 < c) {
        l = Array(c);
        for (var u = 0; u < c; u++) l[u] = arguments[u + 2];
        n.children = l;
      }
      return {
        $$typeof: Vs,
        type: e.type,
        key: s,
        ref: a,
        props: n,
        _owner: o,
      };
    }),
    (J.createContext = function (e) {
      return (
        (e = {
          $$typeof: Ay,
          _currentValue: e,
          _currentValue2: e,
          _threadCount: 0,
          Provider: null,
          Consumer: null,
          _defaultValue: null,
          _globalName: null,
        }),
        (e.Provider = { $$typeof: Ry, _context: e }),
        (e.Consumer = e)
      );
    }),
    (J.createElement = Ad),
    (J.createFactory = function (e) {
      var t = Ad.bind(null, e);
      return ((t.type = e), t);
    }),
    (J.createRef = function () {
      return { current: null };
    }),
    (J.forwardRef = function (e) {
      return { $$typeof: Ey, render: e };
    }),
    (J.isValidElement = Yi),
    (J.lazy = function (e) {
      return { $$typeof: Oy, _payload: { _status: -1, _result: e }, _init: zy };
    }),
    (J.memo = function (e, t) {
      return { $$typeof: Py, type: e, compare: t === void 0 ? null : t };
    }),
    (J.startTransition = function (e) {
      var t = no.transition;
      no.transition = {};
      try {
        e();
      } finally {
        no.transition = t;
      }
    }),
    (J.unstable_act = Td),
    (J.useCallback = function (e, t) {
      return et.current.useCallback(e, t);
    }),
    (J.useContext = function (e) {
      return et.current.useContext(e);
    }),
    (J.useDebugValue = function () {}),
    (J.useDeferredValue = function (e) {
      return et.current.useDeferredValue(e);
    }),
    (J.useEffect = function (e, t) {
      return et.current.useEffect(e, t);
    }),
    (J.useId = function () {
      return et.current.useId();
    }),
    (J.useImperativeHandle = function (e, t, r) {
      return et.current.useImperativeHandle(e, t, r);
    }),
    (J.useInsertionEffect = function (e, t) {
      return et.current.useInsertionEffect(e, t);
    }),
    (J.useLayoutEffect = function (e, t) {
      return et.current.useLayoutEffect(e, t);
    }),
    (J.useMemo = function (e, t) {
      return et.current.useMemo(e, t);
    }),
    (J.useReducer = function (e, t, r) {
      return et.current.useReducer(e, t, r);
    }),
    (J.useRef = function (e) {
      return et.current.useRef(e);
    }),
    (J.useState = function (e) {
      return et.current.useState(e);
    }),
    (J.useSyncExternalStore = function (e, t, r) {
      return et.current.useSyncExternalStore(e, t, r);
    }),
    (J.useTransition = function () {
      return et.current.useTransition();
    }),
    (J.version = "18.3.1"),
    (bd.exports = J));
  var R = bd.exports;
  const pe = _y(R);
  /**
   * @license React
   * react-jsx-runtime.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */ var $y = R,
    Fy = Symbol.for("react.element"),
    Uy = Symbol.for("react.fragment"),
    Zy = Object.prototype.hasOwnProperty,
    By =
      $y.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,
    qy = { key: !0, ref: !0, __self: !0, __source: !0 };
  function Pd(e, t, r) {
    var n,
      s = {},
      a = null,
      o = null;
    (r !== void 0 && (a = "" + r),
      t.key !== void 0 && (a = "" + t.key),
      t.ref !== void 0 && (o = t.ref));
    for (n in t) Zy.call(t, n) && !qy.hasOwnProperty(n) && (s[n] = t[n]);
    if (e && e.defaultProps)
      for (n in ((t = e.defaultProps), t)) s[n] === void 0 && (s[n] = t[n]);
    return {
      $$typeof: Fy,
      type: e,
      key: a,
      ref: o,
      props: s,
      _owner: By.current,
    };
  }
  ((eo.Fragment = Uy), (eo.jsx = Pd), (eo.jsxs = Pd), (xd.exports = eo));
  var i = xd.exports,
    Od = { exports: {} },
    yt = {},
    Id = { exports: {} },
    Ld = {};
  /**
   * @license React
   * scheduler.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */ ((function (e) {
    function t(M, Q) {
      var Y = M.length;
      M.push(Q);
      e: for (; 0 < Y; ) {
        var Ie = (Y - 1) >>> 1,
          qe = M[Ie];
        if (0 < s(qe, Q)) ((M[Ie] = Q), (M[Y] = qe), (Y = Ie));
        else break e;
      }
    }
    function r(M) {
      return M.length === 0 ? null : M[0];
    }
    function n(M) {
      if (M.length === 0) return null;
      var Q = M[0],
        Y = M.pop();
      if (Y !== Q) {
        M[0] = Y;
        e: for (var Ie = 0, qe = M.length, Bi = qe >>> 1; Ie < Bi; ) {
          var Zn = 2 * (Ie + 1) - 1,
            od = M[Zn],
            Bn = Zn + 1,
            qi = M[Bn];
          if (0 > s(od, Y))
            Bn < qe && 0 > s(qi, od)
              ? ((M[Ie] = qi), (M[Bn] = Y), (Ie = Bn))
              : ((M[Ie] = od), (M[Zn] = Y), (Ie = Zn));
          else if (Bn < qe && 0 > s(qi, Y))
            ((M[Ie] = qi), (M[Bn] = Y), (Ie = Bn));
          else break e;
        }
      }
      return Q;
    }
    function s(M, Q) {
      var Y = M.sortIndex - Q.sortIndex;
      return Y !== 0 ? Y : M.id - Q.id;
    }
    if (
      typeof performance == "object" &&
      typeof performance.now == "function"
    ) {
      var a = performance;
      e.unstable_now = function () {
        return a.now();
      };
    } else {
      var o = Date,
        l = o.now();
      e.unstable_now = function () {
        return o.now() - l;
      };
    }
    var c = [],
      u = [],
      d = 1,
      f = null,
      m = 3,
      v = !1,
      x = !1,
      b = !1,
      j = typeof setTimeout == "function" ? setTimeout : null,
      g = typeof clearTimeout == "function" ? clearTimeout : null,
      p = typeof setImmediate < "u" ? setImmediate : null;
    typeof navigator < "u" &&
      navigator.scheduling !== void 0 &&
      navigator.scheduling.isInputPending !== void 0 &&
      navigator.scheduling.isInputPending.bind(navigator.scheduling);
    function y(M) {
      for (var Q = r(u); Q !== null; ) {
        if (Q.callback === null) n(u);
        else if (Q.startTime <= M)
          (n(u), (Q.sortIndex = Q.expirationTime), t(c, Q));
        else break;
        Q = r(u);
      }
    }
    function w(M) {
      if (((b = !1), y(M), !x))
        if (r(c) !== null) ((x = !0), Ya(S));
        else {
          var Q = r(u);
          Q !== null && gt(w, Q.startTime - M);
        }
    }
    function S(M, Q) {
      ((x = !1), b && ((b = !1), g(T), (T = -1)), (v = !0));
      var Y = m;
      try {
        for (
          y(Q), f = r(c);
          f !== null && (!(f.expirationTime > Q) || (M && !q()));
        ) {
          var Ie = f.callback;
          if (typeof Ie == "function") {
            ((f.callback = null), (m = f.priorityLevel));
            var qe = Ie(f.expirationTime <= Q);
            ((Q = e.unstable_now()),
              typeof qe == "function" ? (f.callback = qe) : f === r(c) && n(c),
              y(Q));
          } else n(c);
          f = r(c);
        }
        if (f !== null) var Bi = !0;
        else {
          var Zn = r(u);
          (Zn !== null && gt(w, Zn.startTime - Q), (Bi = !1));
        }
        return Bi;
      } finally {
        ((f = null), (m = Y), (v = !1));
      }
    }
    var A = !1,
      N = null,
      T = -1,
      _ = 5,
      O = -1;
    function q() {
      return !(e.unstable_now() - O < _);
    }
    function ee() {
      if (N !== null) {
        var M = e.unstable_now();
        O = M;
        var Q = !0;
        try {
          Q = N(!0, M);
        } finally {
          Q ? Z() : ((A = !1), (N = null));
        }
      } else A = !1;
    }
    var Z;
    if (typeof p == "function")
      Z = function () {
        p(ee);
      };
    else if (typeof MessageChannel < "u") {
      var Oe = new MessageChannel(),
        nr = Oe.port2;
      ((Oe.port1.onmessage = ee),
        (Z = function () {
          nr.postMessage(null);
        }));
    } else
      Z = function () {
        j(ee, 0);
      };
    function Ya(M) {
      ((N = M), A || ((A = !0), Z()));
    }
    function gt(M, Q) {
      T = j(function () {
        M(e.unstable_now());
      }, Q);
    }
    ((e.unstable_IdlePriority = 5),
      (e.unstable_ImmediatePriority = 1),
      (e.unstable_LowPriority = 4),
      (e.unstable_NormalPriority = 3),
      (e.unstable_Profiling = null),
      (e.unstable_UserBlockingPriority = 2),
      (e.unstable_cancelCallback = function (M) {
        M.callback = null;
      }),
      (e.unstable_continueExecution = function () {
        x || v || ((x = !0), Ya(S));
      }),
      (e.unstable_forceFrameRate = function (M) {
        0 > M || 125 < M
          ? console.error(
              "forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"
            )
          : (_ = 0 < M ? Math.floor(1e3 / M) : 5);
      }),
      (e.unstable_getCurrentPriorityLevel = function () {
        return m;
      }),
      (e.unstable_getFirstCallbackNode = function () {
        return r(c);
      }),
      (e.unstable_next = function (M) {
        switch (m) {
          case 1:
          case 2:
          case 3:
            var Q = 3;
            break;
          default:
            Q = m;
        }
        var Y = m;
        m = Q;
        try {
          return M();
        } finally {
          m = Y;
        }
      }),
      (e.unstable_pauseExecution = function () {}),
      (e.unstable_requestPaint = function () {}),
      (e.unstable_runWithPriority = function (M, Q) {
        switch (M) {
          case 1:
          case 2:
          case 3:
          case 4:
          case 5:
            break;
          default:
            M = 3;
        }
        var Y = m;
        m = M;
        try {
          return Q();
        } finally {
          m = Y;
        }
      }),
      (e.unstable_scheduleCallback = function (M, Q, Y) {
        var Ie = e.unstable_now();
        switch (
          (typeof Y == "object" && Y !== null
            ? ((Y = Y.delay), (Y = typeof Y == "number" && 0 < Y ? Ie + Y : Ie))
            : (Y = Ie),
          M)
        ) {
          case 1:
            var qe = -1;
            break;
          case 2:
            qe = 250;
            break;
          case 5:
            qe = 1073741823;
            break;
          case 4:
            qe = 1e4;
            break;
          default:
            qe = 5e3;
        }
        return (
          (qe = Y + qe),
          (M = {
            id: d++,
            callback: Q,
            priorityLevel: M,
            startTime: Y,
            expirationTime: qe,
            sortIndex: -1,
          }),
          Y > Ie
            ? ((M.sortIndex = Y),
              t(u, M),
              r(c) === null &&
                M === r(u) &&
                (b ? (g(T), (T = -1)) : (b = !0), gt(w, Y - Ie)))
            : ((M.sortIndex = qe), t(c, M), x || v || ((x = !0), Ya(S))),
          M
        );
      }),
      (e.unstable_shouldYield = q),
      (e.unstable_wrapCallback = function (M) {
        var Q = m;
        return function () {
          var Y = m;
          m = Q;
          try {
            return M.apply(this, arguments);
          } finally {
            m = Y;
          }
        };
      }));
  })(Ld),
    (Id.exports = Ld));
  var Vy = Id.exports;
  /**
   * @license React
   * react-dom.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */ var Ky = R,
    vt = Vy;
  function E(e) {
    for (
      var t = "https://reactjs.org/docs/error-decoder.html?invariant=" + e,
        r = 1;
      r < arguments.length;
      r++
    )
      t += "&args[]=" + encodeURIComponent(arguments[r]);
    return (
      "Minified React error #" +
      e +
      "; visit " +
      t +
      " for the full message or use the non-minified dev environment for full errors and additional helpful warnings."
    );
  }
  var Dd = new Set(),
    Ks = {};
  function un(e, t) {
    (Kn(e, t), Kn(e + "Capture", t));
  }
  function Kn(e, t) {
    for (Ks[e] = t, e = 0; e < t.length; e++) Dd.add(t[e]);
  }
  var sr = !(
      typeof window > "u" ||
      typeof window.document > "u" ||
      typeof window.document.createElement > "u"
    ),
    Xi = Object.prototype.hasOwnProperty,
    Wy =
      /^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,
    zd = {},
    Md = {};
  function Hy(e) {
    return Xi.call(Md, e)
      ? !0
      : Xi.call(zd, e)
        ? !1
        : Wy.test(e)
          ? (Md[e] = !0)
          : ((zd[e] = !0), !1);
  }
  function Qy(e, t, r, n) {
    if (r !== null && r.type === 0) return !1;
    switch (typeof t) {
      case "function":
      case "symbol":
        return !0;
      case "boolean":
        return n
          ? !1
          : r !== null
            ? !r.acceptsBooleans
            : ((e = e.toLowerCase().slice(0, 5)),
              e !== "data-" && e !== "aria-");
      default:
        return !1;
    }
  }
  function Gy(e, t, r, n) {
    if (t === null || typeof t > "u" || Qy(e, t, r, n)) return !0;
    if (n) return !1;
    if (r !== null)
      switch (r.type) {
        case 3:
          return !t;
        case 4:
          return t === !1;
        case 5:
          return isNaN(t);
        case 6:
          return isNaN(t) || 1 > t;
      }
    return !1;
  }
  function tt(e, t, r, n, s, a, o) {
    ((this.acceptsBooleans = t === 2 || t === 3 || t === 4),
      (this.attributeName = n),
      (this.attributeNamespace = s),
      (this.mustUseProperty = r),
      (this.propertyName = e),
      (this.type = t),
      (this.sanitizeURL = a),
      (this.removeEmptyString = o));
  }
  var Ve = {};
  ("children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style"
    .split(" ")
    .forEach(function (e) {
      Ve[e] = new tt(e, 0, !1, e, null, !1, !1);
    }),
    [
      ["acceptCharset", "accept-charset"],
      ["className", "class"],
      ["htmlFor", "for"],
      ["httpEquiv", "http-equiv"],
    ].forEach(function (e) {
      var t = e[0];
      Ve[t] = new tt(t, 1, !1, e[1], null, !1, !1);
    }),
    ["contentEditable", "draggable", "spellCheck", "value"].forEach(
      function (e) {
        Ve[e] = new tt(e, 2, !1, e.toLowerCase(), null, !1, !1);
      }
    ),
    [
      "autoReverse",
      "externalResourcesRequired",
      "focusable",
      "preserveAlpha",
    ].forEach(function (e) {
      Ve[e] = new tt(e, 2, !1, e, null, !1, !1);
    }),
    "allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope"
      .split(" ")
      .forEach(function (e) {
        Ve[e] = new tt(e, 3, !1, e.toLowerCase(), null, !1, !1);
      }),
    ["checked", "multiple", "muted", "selected"].forEach(function (e) {
      Ve[e] = new tt(e, 3, !0, e, null, !1, !1);
    }),
    ["capture", "download"].forEach(function (e) {
      Ve[e] = new tt(e, 4, !1, e, null, !1, !1);
    }),
    ["cols", "rows", "size", "span"].forEach(function (e) {
      Ve[e] = new tt(e, 6, !1, e, null, !1, !1);
    }),
    ["rowSpan", "start"].forEach(function (e) {
      Ve[e] = new tt(e, 5, !1, e.toLowerCase(), null, !1, !1);
    }));
  var el = /[\-:]([a-z])/g;
  function tl(e) {
    return e[1].toUpperCase();
  }
  ("accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height"
    .split(" ")
    .forEach(function (e) {
      var t = e.replace(el, tl);
      Ve[t] = new tt(t, 1, !1, e, null, !1, !1);
    }),
    "xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type"
      .split(" ")
      .forEach(function (e) {
        var t = e.replace(el, tl);
        Ve[t] = new tt(t, 1, !1, e, "http://www.w3.org/1999/xlink", !1, !1);
      }),
    ["xml:base", "xml:lang", "xml:space"].forEach(function (e) {
      var t = e.replace(el, tl);
      Ve[t] = new tt(
        t,
        1,
        !1,
        e,
        "http://www.w3.org/XML/1998/namespace",
        !1,
        !1
      );
    }),
    ["tabIndex", "crossOrigin"].forEach(function (e) {
      Ve[e] = new tt(e, 1, !1, e.toLowerCase(), null, !1, !1);
    }),
    (Ve.xlinkHref = new tt(
      "xlinkHref",
      1,
      !1,
      "xlink:href",
      "http://www.w3.org/1999/xlink",
      !0,
      !1
    )),
    ["src", "href", "action", "formAction"].forEach(function (e) {
      Ve[e] = new tt(e, 1, !1, e.toLowerCase(), null, !0, !0);
    }));
  function rl(e, t, r, n) {
    var s = Ve.hasOwnProperty(t) ? Ve[t] : null;
    (s !== null
      ? s.type !== 0
      : n ||
        !(2 < t.length) ||
        (t[0] !== "o" && t[0] !== "O") ||
        (t[1] !== "n" && t[1] !== "N")) &&
      (Gy(t, r, s, n) && (r = null),
      n || s === null
        ? Hy(t) &&
          (r === null ? e.removeAttribute(t) : e.setAttribute(t, "" + r))
        : s.mustUseProperty
          ? (e[s.propertyName] = r === null ? (s.type === 3 ? !1 : "") : r)
          : ((t = s.attributeName),
            (n = s.attributeNamespace),
            r === null
              ? e.removeAttribute(t)
              : ((s = s.type),
                (r = s === 3 || (s === 4 && r === !0) ? "" : "" + r),
                n ? e.setAttributeNS(n, t, r) : e.setAttribute(t, r))));
  }
  var ar = Ky.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,
    so = Symbol.for("react.element"),
    Wn = Symbol.for("react.portal"),
    Hn = Symbol.for("react.fragment"),
    nl = Symbol.for("react.strict_mode"),
    sl = Symbol.for("react.profiler"),
    $d = Symbol.for("react.provider"),
    Fd = Symbol.for("react.context"),
    al = Symbol.for("react.forward_ref"),
    ol = Symbol.for("react.suspense"),
    il = Symbol.for("react.suspense_list"),
    ll = Symbol.for("react.memo"),
    Cr = Symbol.for("react.lazy"),
    Ud = Symbol.for("react.offscreen"),
    Zd = Symbol.iterator;
  function Ws(e) {
    return e === null || typeof e != "object"
      ? null
      : ((e = (Zd && e[Zd]) || e["@@iterator"]),
        typeof e == "function" ? e : null);
  }
  var be = Object.assign,
    cl;
  function Hs(e) {
    if (cl === void 0)
      try {
        throw Error();
      } catch (r) {
        var t = r.stack.trim().match(/\n( *(at )?)/);
        cl = (t && t[1]) || "";
      }
    return (
      `
` +
      cl +
      e
    );
  }
  var ul = !1;
  function dl(e, t) {
    if (!e || ul) return "";
    ul = !0;
    var r = Error.prepareStackTrace;
    Error.prepareStackTrace = void 0;
    try {
      if (t)
        if (
          ((t = function () {
            throw Error();
          }),
          Object.defineProperty(t.prototype, "props", {
            set: function () {
              throw Error();
            },
          }),
          typeof Reflect == "object" && Reflect.construct)
        ) {
          try {
            Reflect.construct(t, []);
          } catch (u) {
            var n = u;
          }
          Reflect.construct(e, [], t);
        } else {
          try {
            t.call();
          } catch (u) {
            n = u;
          }
          e.call(t.prototype);
        }
      else {
        try {
          throw Error();
        } catch (u) {
          n = u;
        }
        e();
      }
    } catch (u) {
      if (u && n && typeof u.stack == "string") {
        for (
          var s = u.stack.split(`
`),
            a = n.stack.split(`
`),
            o = s.length - 1,
            l = a.length - 1;
          1 <= o && 0 <= l && s[o] !== a[l];
        )
          l--;
        for (; 1 <= o && 0 <= l; o--, l--)
          if (s[o] !== a[l]) {
            if (o !== 1 || l !== 1)
              do
                if ((o--, l--, 0 > l || s[o] !== a[l])) {
                  var c =
                    `
` + s[o].replace(" at new ", " at ");
                  return (
                    e.displayName &&
                      c.includes("<anonymous>") &&
                      (c = c.replace("<anonymous>", e.displayName)),
                    c
                  );
                }
              while (1 <= o && 0 <= l);
            break;
          }
      }
    } finally {
      ((ul = !1), (Error.prepareStackTrace = r));
    }
    return (e = e ? e.displayName || e.name : "") ? Hs(e) : "";
  }
  function Yy(e) {
    switch (e.tag) {
      case 5:
        return Hs(e.type);
      case 16:
        return Hs("Lazy");
      case 13:
        return Hs("Suspense");
      case 19:
        return Hs("SuspenseList");
      case 0:
      case 2:
      case 15:
        return ((e = dl(e.type, !1)), e);
      case 11:
        return ((e = dl(e.type.render, !1)), e);
      case 1:
        return ((e = dl(e.type, !0)), e);
      default:
        return "";
    }
  }
  function pl(e) {
    if (e == null) return null;
    if (typeof e == "function") return e.displayName || e.name || null;
    if (typeof e == "string") return e;
    switch (e) {
      case Hn:
        return "Fragment";
      case Wn:
        return "Portal";
      case sl:
        return "Profiler";
      case nl:
        return "StrictMode";
      case ol:
        return "Suspense";
      case il:
        return "SuspenseList";
    }
    if (typeof e == "object")
      switch (e.$$typeof) {
        case Fd:
          return (e.displayName || "Context") + ".Consumer";
        case $d:
          return (e._context.displayName || "Context") + ".Provider";
        case al:
          var t = e.render;
          return (
            (e = e.displayName),
            e ||
              ((e = t.displayName || t.name || ""),
              (e = e !== "" ? "ForwardRef(" + e + ")" : "ForwardRef")),
            e
          );
        case ll:
          return (
            (t = e.displayName || null),
            t !== null ? t : pl(e.type) || "Memo"
          );
        case Cr:
          ((t = e._payload), (e = e._init));
          try {
            return pl(e(t));
          } catch {}
      }
    return null;
  }
  function Jy(e) {
    var t = e.type;
    switch (e.tag) {
      case 24:
        return "Cache";
      case 9:
        return (t.displayName || "Context") + ".Consumer";
      case 10:
        return (t._context.displayName || "Context") + ".Provider";
      case 18:
        return "DehydratedFragment";
      case 11:
        return (
          (e = t.render),
          (e = e.displayName || e.name || ""),
          t.displayName || (e !== "" ? "ForwardRef(" + e + ")" : "ForwardRef")
        );
      case 7:
        return "Fragment";
      case 5:
        return t;
      case 4:
        return "Portal";
      case 3:
        return "Root";
      case 6:
        return "Text";
      case 16:
        return pl(t);
      case 8:
        return t === nl ? "StrictMode" : "Mode";
      case 22:
        return "Offscreen";
      case 12:
        return "Profiler";
      case 21:
        return "Scope";
      case 13:
        return "Suspense";
      case 19:
        return "SuspenseList";
      case 25:
        return "TracingMarker";
      case 1:
      case 0:
      case 17:
      case 2:
      case 14:
      case 15:
        if (typeof t == "function") return t.displayName || t.name || null;
        if (typeof t == "string") return t;
    }
    return null;
  }
  function jr(e) {
    switch (typeof e) {
      case "boolean":
      case "number":
      case "string":
      case "undefined":
        return e;
      case "object":
        return e;
      default:
        return "";
    }
  }
  function Bd(e) {
    var t = e.type;
    return (
      (e = e.nodeName) &&
      e.toLowerCase() === "input" &&
      (t === "checkbox" || t === "radio")
    );
  }
  function Xy(e) {
    var t = Bd(e) ? "checked" : "value",
      r = Object.getOwnPropertyDescriptor(e.constructor.prototype, t),
      n = "" + e[t];
    if (
      !e.hasOwnProperty(t) &&
      typeof r < "u" &&
      typeof r.get == "function" &&
      typeof r.set == "function"
    ) {
      var s = r.get,
        a = r.set;
      return (
        Object.defineProperty(e, t, {
          configurable: !0,
          get: function () {
            return s.call(this);
          },
          set: function (o) {
            ((n = "" + o), a.call(this, o));
          },
        }),
        Object.defineProperty(e, t, { enumerable: r.enumerable }),
        {
          getValue: function () {
            return n;
          },
          setValue: function (o) {
            n = "" + o;
          },
          stopTracking: function () {
            ((e._valueTracker = null), delete e[t]);
          },
        }
      );
    }
  }
  function ao(e) {
    e._valueTracker || (e._valueTracker = Xy(e));
  }
  function qd(e) {
    if (!e) return !1;
    var t = e._valueTracker;
    if (!t) return !0;
    var r = t.getValue(),
      n = "";
    return (
      e && (n = Bd(e) ? (e.checked ? "true" : "false") : e.value),
      (e = n),
      e !== r ? (t.setValue(e), !0) : !1
    );
  }
  function oo(e) {
    if (
      ((e = e || (typeof document < "u" ? document : void 0)), typeof e > "u")
    )
      return null;
    try {
      return e.activeElement || e.body;
    } catch {
      return e.body;
    }
  }
  function ml(e, t) {
    var r = t.checked;
    return be({}, t, {
      defaultChecked: void 0,
      defaultValue: void 0,
      value: void 0,
      checked: r ?? e._wrapperState.initialChecked,
    });
  }
  function Vd(e, t) {
    var r = t.defaultValue == null ? "" : t.defaultValue,
      n = t.checked != null ? t.checked : t.defaultChecked;
    ((r = jr(t.value != null ? t.value : r)),
      (e._wrapperState = {
        initialChecked: n,
        initialValue: r,
        controlled:
          t.type === "checkbox" || t.type === "radio"
            ? t.checked != null
            : t.value != null,
      }));
  }
  function Kd(e, t) {
    ((t = t.checked), t != null && rl(e, "checked", t, !1));
  }
  function fl(e, t) {
    Kd(e, t);
    var r = jr(t.value),
      n = t.type;
    if (r != null)
      n === "number"
        ? ((r === 0 && e.value === "") || e.value != r) && (e.value = "" + r)
        : e.value !== "" + r && (e.value = "" + r);
    else if (n === "submit" || n === "reset") {
      e.removeAttribute("value");
      return;
    }
    (t.hasOwnProperty("value")
      ? hl(e, t.type, r)
      : t.hasOwnProperty("defaultValue") && hl(e, t.type, jr(t.defaultValue)),
      t.checked == null &&
        t.defaultChecked != null &&
        (e.defaultChecked = !!t.defaultChecked));
  }
  function Wd(e, t, r) {
    if (t.hasOwnProperty("value") || t.hasOwnProperty("defaultValue")) {
      var n = t.type;
      if (
        !(
          (n !== "submit" && n !== "reset") ||
          (t.value !== void 0 && t.value !== null)
        )
      )
        return;
      ((t = "" + e._wrapperState.initialValue),
        r || t === e.value || (e.value = t),
        (e.defaultValue = t));
    }
    ((r = e.name),
      r !== "" && (e.name = ""),
      (e.defaultChecked = !!e._wrapperState.initialChecked),
      r !== "" && (e.name = r));
  }
  function hl(e, t, r) {
    (t !== "number" || oo(e.ownerDocument) !== e) &&
      (r == null
        ? (e.defaultValue = "" + e._wrapperState.initialValue)
        : e.defaultValue !== "" + r && (e.defaultValue = "" + r));
  }
  var Qs = Array.isArray;
  function Qn(e, t, r, n) {
    if (((e = e.options), t)) {
      t = {};
      for (var s = 0; s < r.length; s++) t["$" + r[s]] = !0;
      for (r = 0; r < e.length; r++)
        ((s = t.hasOwnProperty("$" + e[r].value)),
          e[r].selected !== s && (e[r].selected = s),
          s && n && (e[r].defaultSelected = !0));
    } else {
      for (r = "" + jr(r), t = null, s = 0; s < e.length; s++) {
        if (e[s].value === r) {
          ((e[s].selected = !0), n && (e[s].defaultSelected = !0));
          return;
        }
        t !== null || e[s].disabled || (t = e[s]);
      }
      t !== null && (t.selected = !0);
    }
  }
  function gl(e, t) {
    if (t.dangerouslySetInnerHTML != null) throw Error(E(91));
    return be({}, t, {
      value: void 0,
      defaultValue: void 0,
      children: "" + e._wrapperState.initialValue,
    });
  }
  function Hd(e, t) {
    var r = t.value;
    if (r == null) {
      if (((r = t.children), (t = t.defaultValue), r != null)) {
        if (t != null) throw Error(E(92));
        if (Qs(r)) {
          if (1 < r.length) throw Error(E(93));
          r = r[0];
        }
        t = r;
      }
      (t == null && (t = ""), (r = t));
    }
    e._wrapperState = { initialValue: jr(r) };
  }
  function Qd(e, t) {
    var r = jr(t.value),
      n = jr(t.defaultValue);
    (r != null &&
      ((r = "" + r),
      r !== e.value && (e.value = r),
      t.defaultValue == null && e.defaultValue !== r && (e.defaultValue = r)),
      n != null && (e.defaultValue = "" + n));
  }
  function Gd(e) {
    var t = e.textContent;
    t === e._wrapperState.initialValue &&
      t !== "" &&
      t !== null &&
      (e.value = t);
  }
  function Yd(e) {
    switch (e) {
      case "svg":
        return "http://www.w3.org/2000/svg";
      case "math":
        return "http://www.w3.org/1998/Math/MathML";
      default:
        return "http://www.w3.org/1999/xhtml";
    }
  }
  function yl(e, t) {
    return e == null || e === "http://www.w3.org/1999/xhtml"
      ? Yd(t)
      : e === "http://www.w3.org/2000/svg" && t === "foreignObject"
        ? "http://www.w3.org/1999/xhtml"
        : e;
  }
  var io,
    Jd = (function (e) {
      return typeof MSApp < "u" && MSApp.execUnsafeLocalFunction
        ? function (t, r, n, s) {
            MSApp.execUnsafeLocalFunction(function () {
              return e(t, r, n, s);
            });
          }
        : e;
    })(function (e, t) {
      if (e.namespaceURI !== "http://www.w3.org/2000/svg" || "innerHTML" in e)
        e.innerHTML = t;
      else {
        for (
          io = io || document.createElement("div"),
            io.innerHTML = "<svg>" + t.valueOf().toString() + "</svg>",
            t = io.firstChild;
          e.firstChild;
        )
          e.removeChild(e.firstChild);
        for (; t.firstChild; ) e.appendChild(t.firstChild);
      }
    });
  function Gs(e, t) {
    if (t) {
      var r = e.firstChild;
      if (r && r === e.lastChild && r.nodeType === 3) {
        r.nodeValue = t;
        return;
      }
    }
    e.textContent = t;
  }
  var Ys = {
      animationIterationCount: !0,
      aspectRatio: !0,
      borderImageOutset: !0,
      borderImageSlice: !0,
      borderImageWidth: !0,
      boxFlex: !0,
      boxFlexGroup: !0,
      boxOrdinalGroup: !0,
      columnCount: !0,
      columns: !0,
      flex: !0,
      flexGrow: !0,
      flexPositive: !0,
      flexShrink: !0,
      flexNegative: !0,
      flexOrder: !0,
      gridArea: !0,
      gridRow: !0,
      gridRowEnd: !0,
      gridRowSpan: !0,
      gridRowStart: !0,
      gridColumn: !0,
      gridColumnEnd: !0,
      gridColumnSpan: !0,
      gridColumnStart: !0,
      fontWeight: !0,
      lineClamp: !0,
      lineHeight: !0,
      opacity: !0,
      order: !0,
      orphans: !0,
      tabSize: !0,
      widows: !0,
      zIndex: !0,
      zoom: !0,
      fillOpacity: !0,
      floodOpacity: !0,
      stopOpacity: !0,
      strokeDasharray: !0,
      strokeDashoffset: !0,
      strokeMiterlimit: !0,
      strokeOpacity: !0,
      strokeWidth: !0,
    },
    e0 = ["Webkit", "ms", "Moz", "O"];
  Object.keys(Ys).forEach(function (e) {
    e0.forEach(function (t) {
      ((t = t + e.charAt(0).toUpperCase() + e.substring(1)), (Ys[t] = Ys[e]));
    });
  });
  function Xd(e, t, r) {
    return t == null || typeof t == "boolean" || t === ""
      ? ""
      : r || typeof t != "number" || t === 0 || (Ys.hasOwnProperty(e) && Ys[e])
        ? ("" + t).trim()
        : t + "px";
  }
  function ep(e, t) {
    e = e.style;
    for (var r in t)
      if (t.hasOwnProperty(r)) {
        var n = r.indexOf("--") === 0,
          s = Xd(r, t[r], n);
        (r === "float" && (r = "cssFloat"),
          n ? e.setProperty(r, s) : (e[r] = s));
      }
  }
  var t0 = be(
    { menuitem: !0 },
    {
      area: !0,
      base: !0,
      br: !0,
      col: !0,
      embed: !0,
      hr: !0,
      img: !0,
      input: !0,
      keygen: !0,
      link: !0,
      meta: !0,
      param: !0,
      source: !0,
      track: !0,
      wbr: !0,
    }
  );
  function vl(e, t) {
    if (t) {
      if (t0[e] && (t.children != null || t.dangerouslySetInnerHTML != null))
        throw Error(E(137, e));
      if (t.dangerouslySetInnerHTML != null) {
        if (t.children != null) throw Error(E(60));
        if (
          typeof t.dangerouslySetInnerHTML != "object" ||
          !("__html" in t.dangerouslySetInnerHTML)
        )
          throw Error(E(61));
      }
      if (t.style != null && typeof t.style != "object") throw Error(E(62));
    }
  }
  function xl(e, t) {
    if (e.indexOf("-") === -1) return typeof t.is == "string";
    switch (e) {
      case "annotation-xml":
      case "color-profile":
      case "font-face":
      case "font-face-src":
      case "font-face-uri":
      case "font-face-format":
      case "font-face-name":
      case "missing-glyph":
        return !1;
      default:
        return !0;
    }
  }
  var bl = null;
  function wl(e) {
    return (
      (e = e.target || e.srcElement || window),
      e.correspondingUseElement && (e = e.correspondingUseElement),
      e.nodeType === 3 ? e.parentNode : e
    );
  }
  var kl = null,
    Gn = null,
    Yn = null;
  function tp(e) {
    if ((e = xa(e))) {
      if (typeof kl != "function") throw Error(E(280));
      var t = e.stateNode;
      t && ((t = To(t)), kl(e.stateNode, e.type, t));
    }
  }
  function rp(e) {
    Gn ? (Yn ? Yn.push(e) : (Yn = [e])) : (Gn = e);
  }
  function np() {
    if (Gn) {
      var e = Gn,
        t = Yn;
      if (((Yn = Gn = null), tp(e), t)) for (e = 0; e < t.length; e++) tp(t[e]);
    }
  }
  function sp(e, t) {
    return e(t);
  }
  function ap() {}
  var _l = !1;
  function op(e, t, r) {
    if (_l) return e(t, r);
    _l = !0;
    try {
      return sp(e, t, r);
    } finally {
      ((_l = !1), (Gn !== null || Yn !== null) && (ap(), np()));
    }
  }
  function Js(e, t) {
    var r = e.stateNode;
    if (r === null) return null;
    var n = To(r);
    if (n === null) return null;
    r = n[t];
    e: switch (t) {
      case "onClick":
      case "onClickCapture":
      case "onDoubleClick":
      case "onDoubleClickCapture":
      case "onMouseDown":
      case "onMouseDownCapture":
      case "onMouseMove":
      case "onMouseMoveCapture":
      case "onMouseUp":
      case "onMouseUpCapture":
      case "onMouseEnter":
        ((n = !n.disabled) ||
          ((e = e.type),
          (n = !(
            e === "button" ||
            e === "input" ||
            e === "select" ||
            e === "textarea"
          ))),
          (e = !n));
        break e;
      default:
        e = !1;
    }
    if (e) return null;
    if (r && typeof r != "function") throw Error(E(231, t, typeof r));
    return r;
  }
  var Sl = !1;
  if (sr)
    try {
      var Xs = {};
      (Object.defineProperty(Xs, "passive", {
        get: function () {
          Sl = !0;
        },
      }),
        window.addEventListener("test", Xs, Xs),
        window.removeEventListener("test", Xs, Xs));
    } catch {
      Sl = !1;
    }
  function r0(e, t, r, n, s, a, o, l, c) {
    var u = Array.prototype.slice.call(arguments, 3);
    try {
      t.apply(r, u);
    } catch (d) {
      this.onError(d);
    }
  }
  var ea = !1,
    lo = null,
    co = !1,
    Cl = null,
    n0 = {
      onError: function (e) {
        ((ea = !0), (lo = e));
      },
    };
  function s0(e, t, r, n, s, a, o, l, c) {
    ((ea = !1), (lo = null), r0.apply(n0, arguments));
  }
  function a0(e, t, r, n, s, a, o, l, c) {
    if ((s0.apply(this, arguments), ea)) {
      if (ea) {
        var u = lo;
        ((ea = !1), (lo = null));
      } else throw Error(E(198));
      co || ((co = !0), (Cl = u));
    }
  }
  function dn(e) {
    var t = e,
      r = e;
    if (e.alternate) for (; t.return; ) t = t.return;
    else {
      e = t;
      do ((t = e), (t.flags & 4098) !== 0 && (r = t.return), (e = t.return));
      while (e);
    }
    return t.tag === 3 ? r : null;
  }
  function ip(e) {
    if (e.tag === 13) {
      var t = e.memoizedState;
      if (
        (t === null && ((e = e.alternate), e !== null && (t = e.memoizedState)),
        t !== null)
      )
        return t.dehydrated;
    }
    return null;
  }
  function lp(e) {
    if (dn(e) !== e) throw Error(E(188));
  }
  function o0(e) {
    var t = e.alternate;
    if (!t) {
      if (((t = dn(e)), t === null)) throw Error(E(188));
      return t !== e ? null : e;
    }
    for (var r = e, n = t; ; ) {
      var s = r.return;
      if (s === null) break;
      var a = s.alternate;
      if (a === null) {
        if (((n = s.return), n !== null)) {
          r = n;
          continue;
        }
        break;
      }
      if (s.child === a.child) {
        for (a = s.child; a; ) {
          if (a === r) return (lp(s), e);
          if (a === n) return (lp(s), t);
          a = a.sibling;
        }
        throw Error(E(188));
      }
      if (r.return !== n.return) ((r = s), (n = a));
      else {
        for (var o = !1, l = s.child; l; ) {
          if (l === r) {
            ((o = !0), (r = s), (n = a));
            break;
          }
          if (l === n) {
            ((o = !0), (n = s), (r = a));
            break;
          }
          l = l.sibling;
        }
        if (!o) {
          for (l = a.child; l; ) {
            if (l === r) {
              ((o = !0), (r = a), (n = s));
              break;
            }
            if (l === n) {
              ((o = !0), (n = a), (r = s));
              break;
            }
            l = l.sibling;
          }
          if (!o) throw Error(E(189));
        }
      }
      if (r.alternate !== n) throw Error(E(190));
    }
    if (r.tag !== 3) throw Error(E(188));
    return r.stateNode.current === r ? e : t;
  }
  function cp(e) {
    return ((e = o0(e)), e !== null ? up(e) : null);
  }
  function up(e) {
    if (e.tag === 5 || e.tag === 6) return e;
    for (e = e.child; e !== null; ) {
      var t = up(e);
      if (t !== null) return t;
      e = e.sibling;
    }
    return null;
  }
  var dp = vt.unstable_scheduleCallback,
    pp = vt.unstable_cancelCallback,
    i0 = vt.unstable_shouldYield,
    l0 = vt.unstable_requestPaint,
    Pe = vt.unstable_now,
    c0 = vt.unstable_getCurrentPriorityLevel,
    jl = vt.unstable_ImmediatePriority,
    mp = vt.unstable_UserBlockingPriority,
    uo = vt.unstable_NormalPriority,
    u0 = vt.unstable_LowPriority,
    fp = vt.unstable_IdlePriority,
    po = null,
    Wt = null;
  function d0(e) {
    if (Wt && typeof Wt.onCommitFiberRoot == "function")
      try {
        Wt.onCommitFiberRoot(po, e, void 0, (e.current.flags & 128) === 128);
      } catch {}
  }
  var Dt = Math.clz32 ? Math.clz32 : f0,
    p0 = Math.log,
    m0 = Math.LN2;
  function f0(e) {
    return ((e >>>= 0), e === 0 ? 32 : (31 - ((p0(e) / m0) | 0)) | 0);
  }
  var mo = 64,
    fo = 4194304;
  function ta(e) {
    switch (e & -e) {
      case 1:
        return 1;
      case 2:
        return 2;
      case 4:
        return 4;
      case 8:
        return 8;
      case 16:
        return 16;
      case 32:
        return 32;
      case 64:
      case 128:
      case 256:
      case 512:
      case 1024:
      case 2048:
      case 4096:
      case 8192:
      case 16384:
      case 32768:
      case 65536:
      case 131072:
      case 262144:
      case 524288:
      case 1048576:
      case 2097152:
        return e & 4194240;
      case 4194304:
      case 8388608:
      case 16777216:
      case 33554432:
      case 67108864:
        return e & 130023424;
      case 134217728:
        return 134217728;
      case 268435456:
        return 268435456;
      case 536870912:
        return 536870912;
      case 1073741824:
        return 1073741824;
      default:
        return e;
    }
  }
  function ho(e, t) {
    var r = e.pendingLanes;
    if (r === 0) return 0;
    var n = 0,
      s = e.suspendedLanes,
      a = e.pingedLanes,
      o = r & 268435455;
    if (o !== 0) {
      var l = o & ~s;
      l !== 0 ? (n = ta(l)) : ((a &= o), a !== 0 && (n = ta(a)));
    } else ((o = r & ~s), o !== 0 ? (n = ta(o)) : a !== 0 && (n = ta(a)));
    if (n === 0) return 0;
    if (
      t !== 0 &&
      t !== n &&
      (t & s) === 0 &&
      ((s = n & -n), (a = t & -t), s >= a || (s === 16 && (a & 4194240) !== 0))
    )
      return t;
    if (((n & 4) !== 0 && (n |= r & 16), (t = e.entangledLanes), t !== 0))
      for (e = e.entanglements, t &= n; 0 < t; )
        ((r = 31 - Dt(t)), (s = 1 << r), (n |= e[r]), (t &= ~s));
    return n;
  }
  function h0(e, t) {
    switch (e) {
      case 1:
      case 2:
      case 4:
        return t + 250;
      case 8:
      case 16:
      case 32:
      case 64:
      case 128:
      case 256:
      case 512:
      case 1024:
      case 2048:
      case 4096:
      case 8192:
      case 16384:
      case 32768:
      case 65536:
      case 131072:
      case 262144:
      case 524288:
      case 1048576:
      case 2097152:
        return t + 5e3;
      case 4194304:
      case 8388608:
      case 16777216:
      case 33554432:
      case 67108864:
        return -1;
      case 134217728:
      case 268435456:
      case 536870912:
      case 1073741824:
        return -1;
      default:
        return -1;
    }
  }
  function g0(e, t) {
    for (
      var r = e.suspendedLanes,
        n = e.pingedLanes,
        s = e.expirationTimes,
        a = e.pendingLanes;
      0 < a;
    ) {
      var o = 31 - Dt(a),
        l = 1 << o,
        c = s[o];
      (c === -1
        ? ((l & r) === 0 || (l & n) !== 0) && (s[o] = h0(l, t))
        : c <= t && (e.expiredLanes |= l),
        (a &= ~l));
    }
  }
  function Nl(e) {
    return (
      (e = e.pendingLanes & -1073741825),
      e !== 0 ? e : e & 1073741824 ? 1073741824 : 0
    );
  }
  function hp() {
    var e = mo;
    return ((mo <<= 1), (mo & 4194240) === 0 && (mo = 64), e);
  }
  function Rl(e) {
    for (var t = [], r = 0; 31 > r; r++) t.push(e);
    return t;
  }
  function ra(e, t, r) {
    ((e.pendingLanes |= t),
      t !== 536870912 && ((e.suspendedLanes = 0), (e.pingedLanes = 0)),
      (e = e.eventTimes),
      (t = 31 - Dt(t)),
      (e[t] = r));
  }
  function y0(e, t) {
    var r = e.pendingLanes & ~t;
    ((e.pendingLanes = t),
      (e.suspendedLanes = 0),
      (e.pingedLanes = 0),
      (e.expiredLanes &= t),
      (e.mutableReadLanes &= t),
      (e.entangledLanes &= t),
      (t = e.entanglements));
    var n = e.eventTimes;
    for (e = e.expirationTimes; 0 < r; ) {
      var s = 31 - Dt(r),
        a = 1 << s;
      ((t[s] = 0), (n[s] = -1), (e[s] = -1), (r &= ~a));
    }
  }
  function Al(e, t) {
    var r = (e.entangledLanes |= t);
    for (e = e.entanglements; r; ) {
      var n = 31 - Dt(r),
        s = 1 << n;
      ((s & t) | (e[n] & t) && (e[n] |= t), (r &= ~s));
    }
  }
  var ce = 0;
  function gp(e) {
    return (
      (e &= -e),
      1 < e ? (4 < e ? ((e & 268435455) !== 0 ? 16 : 536870912) : 4) : 1
    );
  }
  var yp,
    El,
    vp,
    xp,
    bp,
    Tl = !1,
    go = [],
    Nr = null,
    Rr = null,
    Ar = null,
    na = new Map(),
    sa = new Map(),
    Er = [],
    v0 =
      "mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(
        " "
      );
  function wp(e, t) {
    switch (e) {
      case "focusin":
      case "focusout":
        Nr = null;
        break;
      case "dragenter":
      case "dragleave":
        Rr = null;
        break;
      case "mouseover":
      case "mouseout":
        Ar = null;
        break;
      case "pointerover":
      case "pointerout":
        na.delete(t.pointerId);
        break;
      case "gotpointercapture":
      case "lostpointercapture":
        sa.delete(t.pointerId);
    }
  }
  function aa(e, t, r, n, s, a) {
    return e === null || e.nativeEvent !== a
      ? ((e = {
          blockedOn: t,
          domEventName: r,
          eventSystemFlags: n,
          nativeEvent: a,
          targetContainers: [s],
        }),
        t !== null && ((t = xa(t)), t !== null && El(t)),
        e)
      : ((e.eventSystemFlags |= n),
        (t = e.targetContainers),
        s !== null && t.indexOf(s) === -1 && t.push(s),
        e);
  }
  function x0(e, t, r, n, s) {
    switch (t) {
      case "focusin":
        return ((Nr = aa(Nr, e, t, r, n, s)), !0);
      case "dragenter":
        return ((Rr = aa(Rr, e, t, r, n, s)), !0);
      case "mouseover":
        return ((Ar = aa(Ar, e, t, r, n, s)), !0);
      case "pointerover":
        var a = s.pointerId;
        return (na.set(a, aa(na.get(a) || null, e, t, r, n, s)), !0);
      case "gotpointercapture":
        return (
          (a = s.pointerId),
          sa.set(a, aa(sa.get(a) || null, e, t, r, n, s)),
          !0
        );
    }
    return !1;
  }
  function kp(e) {
    var t = pn(e.target);
    if (t !== null) {
      var r = dn(t);
      if (r !== null) {
        if (((t = r.tag), t === 13)) {
          if (((t = ip(r)), t !== null)) {
            ((e.blockedOn = t),
              bp(e.priority, function () {
                vp(r);
              }));
            return;
          }
        } else if (t === 3 && r.stateNode.current.memoizedState.isDehydrated) {
          e.blockedOn = r.tag === 3 ? r.stateNode.containerInfo : null;
          return;
        }
      }
    }
    e.blockedOn = null;
  }
  function yo(e) {
    if (e.blockedOn !== null) return !1;
    for (var t = e.targetContainers; 0 < t.length; ) {
      var r = Ol(e.domEventName, e.eventSystemFlags, t[0], e.nativeEvent);
      if (r === null) {
        r = e.nativeEvent;
        var n = new r.constructor(r.type, r);
        ((bl = n), r.target.dispatchEvent(n), (bl = null));
      } else return ((t = xa(r)), t !== null && El(t), (e.blockedOn = r), !1);
      t.shift();
    }
    return !0;
  }
  function _p(e, t, r) {
    yo(e) && r.delete(t);
  }
  function b0() {
    ((Tl = !1),
      Nr !== null && yo(Nr) && (Nr = null),
      Rr !== null && yo(Rr) && (Rr = null),
      Ar !== null && yo(Ar) && (Ar = null),
      na.forEach(_p),
      sa.forEach(_p));
  }
  function oa(e, t) {
    e.blockedOn === t &&
      ((e.blockedOn = null),
      Tl ||
        ((Tl = !0),
        vt.unstable_scheduleCallback(vt.unstable_NormalPriority, b0)));
  }
  function ia(e) {
    function t(s) {
      return oa(s, e);
    }
    if (0 < go.length) {
      oa(go[0], e);
      for (var r = 1; r < go.length; r++) {
        var n = go[r];
        n.blockedOn === e && (n.blockedOn = null);
      }
    }
    for (
      Nr !== null && oa(Nr, e),
        Rr !== null && oa(Rr, e),
        Ar !== null && oa(Ar, e),
        na.forEach(t),
        sa.forEach(t),
        r = 0;
      r < Er.length;
      r++
    )
      ((n = Er[r]), n.blockedOn === e && (n.blockedOn = null));
    for (; 0 < Er.length && ((r = Er[0]), r.blockedOn === null); )
      (kp(r), r.blockedOn === null && Er.shift());
  }
  var Jn = ar.ReactCurrentBatchConfig,
    vo = !0;
  function w0(e, t, r, n) {
    var s = ce,
      a = Jn.transition;
    Jn.transition = null;
    try {
      ((ce = 1), Pl(e, t, r, n));
    } finally {
      ((ce = s), (Jn.transition = a));
    }
  }
  function k0(e, t, r, n) {
    var s = ce,
      a = Jn.transition;
    Jn.transition = null;
    try {
      ((ce = 4), Pl(e, t, r, n));
    } finally {
      ((ce = s), (Jn.transition = a));
    }
  }
  function Pl(e, t, r, n) {
    if (vo) {
      var s = Ol(e, t, r, n);
      if (s === null) (Gl(e, t, n, xo, r), wp(e, n));
      else if (x0(s, e, t, r, n)) n.stopPropagation();
      else if ((wp(e, n), t & 4 && -1 < v0.indexOf(e))) {
        for (; s !== null; ) {
          var a = xa(s);
          if (
            (a !== null && yp(a),
            (a = Ol(e, t, r, n)),
            a === null && Gl(e, t, n, xo, r),
            a === s)
          )
            break;
          s = a;
        }
        s !== null && n.stopPropagation();
      } else Gl(e, t, n, null, r);
    }
  }
  var xo = null;
  function Ol(e, t, r, n) {
    if (((xo = null), (e = wl(n)), (e = pn(e)), e !== null))
      if (((t = dn(e)), t === null)) e = null;
      else if (((r = t.tag), r === 13)) {
        if (((e = ip(t)), e !== null)) return e;
        e = null;
      } else if (r === 3) {
        if (t.stateNode.current.memoizedState.isDehydrated)
          return t.tag === 3 ? t.stateNode.containerInfo : null;
        e = null;
      } else t !== e && (e = null);
    return ((xo = e), null);
  }
  function Sp(e) {
    switch (e) {
      case "cancel":
      case "click":
      case "close":
      case "contextmenu":
      case "copy":
      case "cut":
      case "auxclick":
      case "dblclick":
      case "dragend":
      case "dragstart":
      case "drop":
      case "focusin":
      case "focusout":
      case "input":
      case "invalid":
      case "keydown":
      case "keypress":
      case "keyup":
      case "mousedown":
      case "mouseup":
      case "paste":
      case "pause":
      case "play":
      case "pointercancel":
      case "pointerdown":
      case "pointerup":
      case "ratechange":
      case "reset":
      case "resize":
      case "seeked":
      case "submit":
      case "touchcancel":
      case "touchend":
      case "touchstart":
      case "volumechange":
      case "change":
      case "selectionchange":
      case "textInput":
      case "compositionstart":
      case "compositionend":
      case "compositionupdate":
      case "beforeblur":
      case "afterblur":
      case "beforeinput":
      case "blur":
      case "fullscreenchange":
      case "focus":
      case "hashchange":
      case "popstate":
      case "select":
      case "selectstart":
        return 1;
      case "drag":
      case "dragenter":
      case "dragexit":
      case "dragleave":
      case "dragover":
      case "mousemove":
      case "mouseout":
      case "mouseover":
      case "pointermove":
      case "pointerout":
      case "pointerover":
      case "scroll":
      case "toggle":
      case "touchmove":
      case "wheel":
      case "mouseenter":
      case "mouseleave":
      case "pointerenter":
      case "pointerleave":
        return 4;
      case "message":
        switch (c0()) {
          case jl:
            return 1;
          case mp:
            return 4;
          case uo:
          case u0:
            return 16;
          case fp:
            return 536870912;
          default:
            return 16;
        }
      default:
        return 16;
    }
  }
  var Tr = null,
    Il = null,
    bo = null;
  function Cp() {
    if (bo) return bo;
    var e,
      t = Il,
      r = t.length,
      n,
      s = "value" in Tr ? Tr.value : Tr.textContent,
      a = s.length;
    for (e = 0; e < r && t[e] === s[e]; e++);
    var o = r - e;
    for (n = 1; n <= o && t[r - n] === s[a - n]; n++);
    return (bo = s.slice(e, 1 < n ? 1 - n : void 0));
  }
  function wo(e) {
    var t = e.keyCode;
    return (
      "charCode" in e
        ? ((e = e.charCode), e === 0 && t === 13 && (e = 13))
        : (e = t),
      e === 10 && (e = 13),
      32 <= e || e === 13 ? e : 0
    );
  }
  function ko() {
    return !0;
  }
  function jp() {
    return !1;
  }
  function xt(e) {
    function t(r, n, s, a, o) {
      ((this._reactName = r),
        (this._targetInst = s),
        (this.type = n),
        (this.nativeEvent = a),
        (this.target = o),
        (this.currentTarget = null));
      for (var l in e)
        e.hasOwnProperty(l) && ((r = e[l]), (this[l] = r ? r(a) : a[l]));
      return (
        (this.isDefaultPrevented = (
          a.defaultPrevented != null ? a.defaultPrevented : a.returnValue === !1
        )
          ? ko
          : jp),
        (this.isPropagationStopped = jp),
        this
      );
    }
    return (
      be(t.prototype, {
        preventDefault: function () {
          this.defaultPrevented = !0;
          var r = this.nativeEvent;
          r &&
            (r.preventDefault
              ? r.preventDefault()
              : typeof r.returnValue != "unknown" && (r.returnValue = !1),
            (this.isDefaultPrevented = ko));
        },
        stopPropagation: function () {
          var r = this.nativeEvent;
          r &&
            (r.stopPropagation
              ? r.stopPropagation()
              : typeof r.cancelBubble != "unknown" && (r.cancelBubble = !0),
            (this.isPropagationStopped = ko));
        },
        persist: function () {},
        isPersistent: ko,
      }),
      t
    );
  }
  var Xn = {
      eventPhase: 0,
      bubbles: 0,
      cancelable: 0,
      timeStamp: function (e) {
        return e.timeStamp || Date.now();
      },
      defaultPrevented: 0,
      isTrusted: 0,
    },
    Ll = xt(Xn),
    la = be({}, Xn, { view: 0, detail: 0 }),
    _0 = xt(la),
    Dl,
    zl,
    ca,
    _o = be({}, la, {
      screenX: 0,
      screenY: 0,
      clientX: 0,
      clientY: 0,
      pageX: 0,
      pageY: 0,
      ctrlKey: 0,
      shiftKey: 0,
      altKey: 0,
      metaKey: 0,
      getModifierState: $l,
      button: 0,
      buttons: 0,
      relatedTarget: function (e) {
        return e.relatedTarget === void 0
          ? e.fromElement === e.srcElement
            ? e.toElement
            : e.fromElement
          : e.relatedTarget;
      },
      movementX: function (e) {
        return "movementX" in e
          ? e.movementX
          : (e !== ca &&
              (ca && e.type === "mousemove"
                ? ((Dl = e.screenX - ca.screenX), (zl = e.screenY - ca.screenY))
                : (zl = Dl = 0),
              (ca = e)),
            Dl);
      },
      movementY: function (e) {
        return "movementY" in e ? e.movementY : zl;
      },
    }),
    Np = xt(_o),
    S0 = be({}, _o, { dataTransfer: 0 }),
    C0 = xt(S0),
    j0 = be({}, la, { relatedTarget: 0 }),
    Ml = xt(j0),
    N0 = be({}, Xn, { animationName: 0, elapsedTime: 0, pseudoElement: 0 }),
    R0 = xt(N0),
    A0 = be({}, Xn, {
      clipboardData: function (e) {
        return "clipboardData" in e ? e.clipboardData : window.clipboardData;
      },
    }),
    E0 = xt(A0),
    T0 = be({}, Xn, { data: 0 }),
    Rp = xt(T0),
    P0 = {
      Esc: "Escape",
      Spacebar: " ",
      Left: "ArrowLeft",
      Up: "ArrowUp",
      Right: "ArrowRight",
      Down: "ArrowDown",
      Del: "Delete",
      Win: "OS",
      Menu: "ContextMenu",
      Apps: "ContextMenu",
      Scroll: "ScrollLock",
      MozPrintableKey: "Unidentified",
    },
    O0 = {
      8: "Backspace",
      9: "Tab",
      12: "Clear",
      13: "Enter",
      16: "Shift",
      17: "Control",
      18: "Alt",
      19: "Pause",
      20: "CapsLock",
      27: "Escape",
      32: " ",
      33: "PageUp",
      34: "PageDown",
      35: "End",
      36: "Home",
      37: "ArrowLeft",
      38: "ArrowUp",
      39: "ArrowRight",
      40: "ArrowDown",
      45: "Insert",
      46: "Delete",
      112: "F1",
      113: "F2",
      114: "F3",
      115: "F4",
      116: "F5",
      117: "F6",
      118: "F7",
      119: "F8",
      120: "F9",
      121: "F10",
      122: "F11",
      123: "F12",
      144: "NumLock",
      145: "ScrollLock",
      224: "Meta",
    },
    I0 = {
      Alt: "altKey",
      Control: "ctrlKey",
      Meta: "metaKey",
      Shift: "shiftKey",
    };
  function L0(e) {
    var t = this.nativeEvent;
    return t.getModifierState
      ? t.getModifierState(e)
      : (e = I0[e])
        ? !!t[e]
        : !1;
  }
  function $l() {
    return L0;
  }
  var D0 = be({}, la, {
      key: function (e) {
        if (e.key) {
          var t = P0[e.key] || e.key;
          if (t !== "Unidentified") return t;
        }
        return e.type === "keypress"
          ? ((e = wo(e)), e === 13 ? "Enter" : String.fromCharCode(e))
          : e.type === "keydown" || e.type === "keyup"
            ? O0[e.keyCode] || "Unidentified"
            : "";
      },
      code: 0,
      location: 0,
      ctrlKey: 0,
      shiftKey: 0,
      altKey: 0,
      metaKey: 0,
      repeat: 0,
      locale: 0,
      getModifierState: $l,
      charCode: function (e) {
        return e.type === "keypress" ? wo(e) : 0;
      },
      keyCode: function (e) {
        return e.type === "keydown" || e.type === "keyup" ? e.keyCode : 0;
      },
      which: function (e) {
        return e.type === "keypress"
          ? wo(e)
          : e.type === "keydown" || e.type === "keyup"
            ? e.keyCode
            : 0;
      },
    }),
    z0 = xt(D0),
    M0 = be({}, _o, {
      pointerId: 0,
      width: 0,
      height: 0,
      pressure: 0,
      tangentialPressure: 0,
      tiltX: 0,
      tiltY: 0,
      twist: 0,
      pointerType: 0,
      isPrimary: 0,
    }),
    Ap = xt(M0),
    $0 = be({}, la, {
      touches: 0,
      targetTouches: 0,
      changedTouches: 0,
      altKey: 0,
      metaKey: 0,
      ctrlKey: 0,
      shiftKey: 0,
      getModifierState: $l,
    }),
    F0 = xt($0),
    U0 = be({}, Xn, { propertyName: 0, elapsedTime: 0, pseudoElement: 0 }),
    Z0 = xt(U0),
    B0 = be({}, _o, {
      deltaX: function (e) {
        return "deltaX" in e
          ? e.deltaX
          : "wheelDeltaX" in e
            ? -e.wheelDeltaX
            : 0;
      },
      deltaY: function (e) {
        return "deltaY" in e
          ? e.deltaY
          : "wheelDeltaY" in e
            ? -e.wheelDeltaY
            : "wheelDelta" in e
              ? -e.wheelDelta
              : 0;
      },
      deltaZ: 0,
      deltaMode: 0,
    }),
    q0 = xt(B0),
    V0 = [9, 13, 27, 32],
    Fl = sr && "CompositionEvent" in window,
    ua = null;
  sr && "documentMode" in document && (ua = document.documentMode);
  var K0 = sr && "TextEvent" in window && !ua,
    Ep = sr && (!Fl || (ua && 8 < ua && 11 >= ua)),
    Tp = " ",
    Pp = !1;
  function Op(e, t) {
    switch (e) {
      case "keyup":
        return V0.indexOf(t.keyCode) !== -1;
      case "keydown":
        return t.keyCode !== 229;
      case "keypress":
      case "mousedown":
      case "focusout":
        return !0;
      default:
        return !1;
    }
  }
  function Ip(e) {
    return (
      (e = e.detail),
      typeof e == "object" && "data" in e ? e.data : null
    );
  }
  var es = !1;
  function W0(e, t) {
    switch (e) {
      case "compositionend":
        return Ip(t);
      case "keypress":
        return t.which !== 32 ? null : ((Pp = !0), Tp);
      case "textInput":
        return ((e = t.data), e === Tp && Pp ? null : e);
      default:
        return null;
    }
  }
  function H0(e, t) {
    if (es)
      return e === "compositionend" || (!Fl && Op(e, t))
        ? ((e = Cp()), (bo = Il = Tr = null), (es = !1), e)
        : null;
    switch (e) {
      case "paste":
        return null;
      case "keypress":
        if (!(t.ctrlKey || t.altKey || t.metaKey) || (t.ctrlKey && t.altKey)) {
          if (t.char && 1 < t.char.length) return t.char;
          if (t.which) return String.fromCharCode(t.which);
        }
        return null;
      case "compositionend":
        return Ep && t.locale !== "ko" ? null : t.data;
      default:
        return null;
    }
  }
  var Q0 = {
    color: !0,
    date: !0,
    datetime: !0,
    "datetime-local": !0,
    email: !0,
    month: !0,
    number: !0,
    password: !0,
    range: !0,
    search: !0,
    tel: !0,
    text: !0,
    time: !0,
    url: !0,
    week: !0,
  };
  function Lp(e) {
    var t = e && e.nodeName && e.nodeName.toLowerCase();
    return t === "input" ? !!Q0[e.type] : t === "textarea";
  }
  function Dp(e, t, r, n) {
    (rp(n),
      (t = Ro(t, "onChange")),
      0 < t.length &&
        ((r = new Ll("onChange", "change", null, r, n)),
        e.push({ event: r, listeners: t })));
  }
  var da = null,
    pa = null;
  function G0(e) {
    em(e, 0);
  }
  function So(e) {
    var t = as(e);
    if (qd(t)) return e;
  }
  function Y0(e, t) {
    if (e === "change") return t;
  }
  var zp = !1;
  if (sr) {
    var Ul;
    if (sr) {
      var Zl = "oninput" in document;
      if (!Zl) {
        var Mp = document.createElement("div");
        (Mp.setAttribute("oninput", "return;"),
          (Zl = typeof Mp.oninput == "function"));
      }
      Ul = Zl;
    } else Ul = !1;
    zp = Ul && (!document.documentMode || 9 < document.documentMode);
  }
  function $p() {
    da && (da.detachEvent("onpropertychange", Fp), (pa = da = null));
  }
  function Fp(e) {
    if (e.propertyName === "value" && So(pa)) {
      var t = [];
      (Dp(t, pa, e, wl(e)), op(G0, t));
    }
  }
  function J0(e, t, r) {
    e === "focusin"
      ? ($p(), (da = t), (pa = r), da.attachEvent("onpropertychange", Fp))
      : e === "focusout" && $p();
  }
  function X0(e) {
    if (e === "selectionchange" || e === "keyup" || e === "keydown")
      return So(pa);
  }
  function ev(e, t) {
    if (e === "click") return So(t);
  }
  function tv(e, t) {
    if (e === "input" || e === "change") return So(t);
  }
  function rv(e, t) {
    return (e === t && (e !== 0 || 1 / e === 1 / t)) || (e !== e && t !== t);
  }
  var zt = typeof Object.is == "function" ? Object.is : rv;
  function ma(e, t) {
    if (zt(e, t)) return !0;
    if (
      typeof e != "object" ||
      e === null ||
      typeof t != "object" ||
      t === null
    )
      return !1;
    var r = Object.keys(e),
      n = Object.keys(t);
    if (r.length !== n.length) return !1;
    for (n = 0; n < r.length; n++) {
      var s = r[n];
      if (!Xi.call(t, s) || !zt(e[s], t[s])) return !1;
    }
    return !0;
  }
  function Up(e) {
    for (; e && e.firstChild; ) e = e.firstChild;
    return e;
  }
  function Zp(e, t) {
    var r = Up(e);
    e = 0;
    for (var n; r; ) {
      if (r.nodeType === 3) {
        if (((n = e + r.textContent.length), e <= t && n >= t))
          return { node: r, offset: t - e };
        e = n;
      }
      e: {
        for (; r; ) {
          if (r.nextSibling) {
            r = r.nextSibling;
            break e;
          }
          r = r.parentNode;
        }
        r = void 0;
      }
      r = Up(r);
    }
  }
  function Bp(e, t) {
    return e && t
      ? e === t
        ? !0
        : e && e.nodeType === 3
          ? !1
          : t && t.nodeType === 3
            ? Bp(e, t.parentNode)
            : "contains" in e
              ? e.contains(t)
              : e.compareDocumentPosition
                ? !!(e.compareDocumentPosition(t) & 16)
                : !1
      : !1;
  }
  function qp() {
    for (var e = window, t = oo(); t instanceof e.HTMLIFrameElement; ) {
      try {
        var r = typeof t.contentWindow.location.href == "string";
      } catch {
        r = !1;
      }
      if (r) e = t.contentWindow;
      else break;
      t = oo(e.document);
    }
    return t;
  }
  function Bl(e) {
    var t = e && e.nodeName && e.nodeName.toLowerCase();
    return (
      t &&
      ((t === "input" &&
        (e.type === "text" ||
          e.type === "search" ||
          e.type === "tel" ||
          e.type === "url" ||
          e.type === "password")) ||
        t === "textarea" ||
        e.contentEditable === "true")
    );
  }
  function nv(e) {
    var t = qp(),
      r = e.focusedElem,
      n = e.selectionRange;
    if (
      t !== r &&
      r &&
      r.ownerDocument &&
      Bp(r.ownerDocument.documentElement, r)
    ) {
      if (n !== null && Bl(r)) {
        if (
          ((t = n.start),
          (e = n.end),
          e === void 0 && (e = t),
          "selectionStart" in r)
        )
          ((r.selectionStart = t),
            (r.selectionEnd = Math.min(e, r.value.length)));
        else if (
          ((e = ((t = r.ownerDocument || document) && t.defaultView) || window),
          e.getSelection)
        ) {
          e = e.getSelection();
          var s = r.textContent.length,
            a = Math.min(n.start, s);
          ((n = n.end === void 0 ? a : Math.min(n.end, s)),
            !e.extend && a > n && ((s = n), (n = a), (a = s)),
            (s = Zp(r, a)));
          var o = Zp(r, n);
          s &&
            o &&
            (e.rangeCount !== 1 ||
              e.anchorNode !== s.node ||
              e.anchorOffset !== s.offset ||
              e.focusNode !== o.node ||
              e.focusOffset !== o.offset) &&
            ((t = t.createRange()),
            t.setStart(s.node, s.offset),
            e.removeAllRanges(),
            a > n
              ? (e.addRange(t), e.extend(o.node, o.offset))
              : (t.setEnd(o.node, o.offset), e.addRange(t)));
        }
      }
      for (t = [], e = r; (e = e.parentNode); )
        e.nodeType === 1 &&
          t.push({ element: e, left: e.scrollLeft, top: e.scrollTop });
      for (typeof r.focus == "function" && r.focus(), r = 0; r < t.length; r++)
        ((e = t[r]),
          (e.element.scrollLeft = e.left),
          (e.element.scrollTop = e.top));
    }
  }
  var sv = sr && "documentMode" in document && 11 >= document.documentMode,
    ts = null,
    ql = null,
    fa = null,
    Vl = !1;
  function Vp(e, t, r) {
    var n =
      r.window === r ? r.document : r.nodeType === 9 ? r : r.ownerDocument;
    Vl ||
      ts == null ||
      ts !== oo(n) ||
      ((n = ts),
      "selectionStart" in n && Bl(n)
        ? (n = { start: n.selectionStart, end: n.selectionEnd })
        : ((n = (
            (n.ownerDocument && n.ownerDocument.defaultView) ||
            window
          ).getSelection()),
          (n = {
            anchorNode: n.anchorNode,
            anchorOffset: n.anchorOffset,
            focusNode: n.focusNode,
            focusOffset: n.focusOffset,
          })),
      (fa && ma(fa, n)) ||
        ((fa = n),
        (n = Ro(ql, "onSelect")),
        0 < n.length &&
          ((t = new Ll("onSelect", "select", null, t, r)),
          e.push({ event: t, listeners: n }),
          (t.target = ts))));
  }
  function Co(e, t) {
    var r = {};
    return (
      (r[e.toLowerCase()] = t.toLowerCase()),
      (r["Webkit" + e] = "webkit" + t),
      (r["Moz" + e] = "moz" + t),
      r
    );
  }
  var rs = {
      animationend: Co("Animation", "AnimationEnd"),
      animationiteration: Co("Animation", "AnimationIteration"),
      animationstart: Co("Animation", "AnimationStart"),
      transitionend: Co("Transition", "TransitionEnd"),
    },
    Kl = {},
    Kp = {};
  sr &&
    ((Kp = document.createElement("div").style),
    "AnimationEvent" in window ||
      (delete rs.animationend.animation,
      delete rs.animationiteration.animation,
      delete rs.animationstart.animation),
    "TransitionEvent" in window || delete rs.transitionend.transition);
  function jo(e) {
    if (Kl[e]) return Kl[e];
    if (!rs[e]) return e;
    var t = rs[e],
      r;
    for (r in t) if (t.hasOwnProperty(r) && r in Kp) return (Kl[e] = t[r]);
    return e;
  }
  var Wp = jo("animationend"),
    Hp = jo("animationiteration"),
    Qp = jo("animationstart"),
    Gp = jo("transitionend"),
    Yp = new Map(),
    Jp =
      "abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(
        " "
      );
  function Pr(e, t) {
    (Yp.set(e, t), un(t, [e]));
  }
  for (var Wl = 0; Wl < Jp.length; Wl++) {
    var Hl = Jp[Wl],
      av = Hl.toLowerCase(),
      ov = Hl[0].toUpperCase() + Hl.slice(1);
    Pr(av, "on" + ov);
  }
  (Pr(Wp, "onAnimationEnd"),
    Pr(Hp, "onAnimationIteration"),
    Pr(Qp, "onAnimationStart"),
    Pr("dblclick", "onDoubleClick"),
    Pr("focusin", "onFocus"),
    Pr("focusout", "onBlur"),
    Pr(Gp, "onTransitionEnd"),
    Kn("onMouseEnter", ["mouseout", "mouseover"]),
    Kn("onMouseLeave", ["mouseout", "mouseover"]),
    Kn("onPointerEnter", ["pointerout", "pointerover"]),
    Kn("onPointerLeave", ["pointerout", "pointerover"]),
    un(
      "onChange",
      "change click focusin focusout input keydown keyup selectionchange".split(
        " "
      )
    ),
    un(
      "onSelect",
      "focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(
        " "
      )
    ),
    un("onBeforeInput", ["compositionend", "keypress", "textInput", "paste"]),
    un(
      "onCompositionEnd",
      "compositionend focusout keydown keypress keyup mousedown".split(" ")
    ),
    un(
      "onCompositionStart",
      "compositionstart focusout keydown keypress keyup mousedown".split(" ")
    ),
    un(
      "onCompositionUpdate",
      "compositionupdate focusout keydown keypress keyup mousedown".split(" ")
    ));
  var ha =
      "abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(
        " "
      ),
    iv = new Set(
      "cancel close invalid load scroll toggle".split(" ").concat(ha)
    );
  function Xp(e, t, r) {
    var n = e.type || "unknown-event";
    ((e.currentTarget = r), a0(n, t, void 0, e), (e.currentTarget = null));
  }
  function em(e, t) {
    t = (t & 4) !== 0;
    for (var r = 0; r < e.length; r++) {
      var n = e[r],
        s = n.event;
      n = n.listeners;
      e: {
        var a = void 0;
        if (t)
          for (var o = n.length - 1; 0 <= o; o--) {
            var l = n[o],
              c = l.instance,
              u = l.currentTarget;
            if (((l = l.listener), c !== a && s.isPropagationStopped()))
              break e;
            (Xp(s, l, u), (a = c));
          }
        else
          for (o = 0; o < n.length; o++) {
            if (
              ((l = n[o]),
              (c = l.instance),
              (u = l.currentTarget),
              (l = l.listener),
              c !== a && s.isPropagationStopped())
            )
              break e;
            (Xp(s, l, u), (a = c));
          }
      }
    }
    if (co) throw ((e = Cl), (co = !1), (Cl = null), e);
  }
  function he(e, t) {
    var r = t[rc];
    r === void 0 && (r = t[rc] = new Set());
    var n = e + "__bubble";
    r.has(n) || (tm(t, e, 2, !1), r.add(n));
  }
  function Ql(e, t, r) {
    var n = 0;
    (t && (n |= 4), tm(r, e, n, t));
  }
  var No = "_reactListening" + Math.random().toString(36).slice(2);
  function ga(e) {
    if (!e[No]) {
      ((e[No] = !0),
        Dd.forEach(function (r) {
          r !== "selectionchange" && (iv.has(r) || Ql(r, !1, e), Ql(r, !0, e));
        }));
      var t = e.nodeType === 9 ? e : e.ownerDocument;
      t === null || t[No] || ((t[No] = !0), Ql("selectionchange", !1, t));
    }
  }
  function tm(e, t, r, n) {
    switch (Sp(t)) {
      case 1:
        var s = w0;
        break;
      case 4:
        s = k0;
        break;
      default:
        s = Pl;
    }
    ((r = s.bind(null, t, r, e)),
      (s = void 0),
      !Sl ||
        (t !== "touchstart" && t !== "touchmove" && t !== "wheel") ||
        (s = !0),
      n
        ? s !== void 0
          ? e.addEventListener(t, r, { capture: !0, passive: s })
          : e.addEventListener(t, r, !0)
        : s !== void 0
          ? e.addEventListener(t, r, { passive: s })
          : e.addEventListener(t, r, !1));
  }
  function Gl(e, t, r, n, s) {
    var a = n;
    if ((t & 1) === 0 && (t & 2) === 0 && n !== null)
      e: for (;;) {
        if (n === null) return;
        var o = n.tag;
        if (o === 3 || o === 4) {
          var l = n.stateNode.containerInfo;
          if (l === s || (l.nodeType === 8 && l.parentNode === s)) break;
          if (o === 4)
            for (o = n.return; o !== null; ) {
              var c = o.tag;
              if (
                (c === 3 || c === 4) &&
                ((c = o.stateNode.containerInfo),
                c === s || (c.nodeType === 8 && c.parentNode === s))
              )
                return;
              o = o.return;
            }
          for (; l !== null; ) {
            if (((o = pn(l)), o === null)) return;
            if (((c = o.tag), c === 5 || c === 6)) {
              n = a = o;
              continue e;
            }
            l = l.parentNode;
          }
        }
        n = n.return;
      }
    op(function () {
      var u = a,
        d = wl(r),
        f = [];
      e: {
        var m = Yp.get(e);
        if (m !== void 0) {
          var v = Ll,
            x = e;
          switch (e) {
            case "keypress":
              if (wo(r) === 0) break e;
            case "keydown":
            case "keyup":
              v = z0;
              break;
            case "focusin":
              ((x = "focus"), (v = Ml));
              break;
            case "focusout":
              ((x = "blur"), (v = Ml));
              break;
            case "beforeblur":
            case "afterblur":
              v = Ml;
              break;
            case "click":
              if (r.button === 2) break e;
            case "auxclick":
            case "dblclick":
            case "mousedown":
            case "mousemove":
            case "mouseup":
            case "mouseout":
            case "mouseover":
            case "contextmenu":
              v = Np;
              break;
            case "drag":
            case "dragend":
            case "dragenter":
            case "dragexit":
            case "dragleave":
            case "dragover":
            case "dragstart":
            case "drop":
              v = C0;
              break;
            case "touchcancel":
            case "touchend":
            case "touchmove":
            case "touchstart":
              v = F0;
              break;
            case Wp:
            case Hp:
            case Qp:
              v = R0;
              break;
            case Gp:
              v = Z0;
              break;
            case "scroll":
              v = _0;
              break;
            case "wheel":
              v = q0;
              break;
            case "copy":
            case "cut":
            case "paste":
              v = E0;
              break;
            case "gotpointercapture":
            case "lostpointercapture":
            case "pointercancel":
            case "pointerdown":
            case "pointermove":
            case "pointerout":
            case "pointerover":
            case "pointerup":
              v = Ap;
          }
          var b = (t & 4) !== 0,
            j = !b && e === "scroll",
            g = b ? (m !== null ? m + "Capture" : null) : m;
          b = [];
          for (var p = u, y; p !== null; ) {
            y = p;
            var w = y.stateNode;
            if (
              (y.tag === 5 &&
                w !== null &&
                ((y = w),
                g !== null &&
                  ((w = Js(p, g)), w != null && b.push(ya(p, w, y)))),
              j)
            )
              break;
            p = p.return;
          }
          0 < b.length &&
            ((m = new v(m, x, null, r, d)), f.push({ event: m, listeners: b }));
        }
      }
      if ((t & 7) === 0) {
        e: {
          if (
            ((m = e === "mouseover" || e === "pointerover"),
            (v = e === "mouseout" || e === "pointerout"),
            m &&
              r !== bl &&
              (x = r.relatedTarget || r.fromElement) &&
              (pn(x) || x[or]))
          )
            break e;
          if (
            (v || m) &&
            ((m =
              d.window === d
                ? d
                : (m = d.ownerDocument)
                  ? m.defaultView || m.parentWindow
                  : window),
            v
              ? ((x = r.relatedTarget || r.toElement),
                (v = u),
                (x = x ? pn(x) : null),
                x !== null &&
                  ((j = dn(x)), x !== j || (x.tag !== 5 && x.tag !== 6)) &&
                  (x = null))
              : ((v = null), (x = u)),
            v !== x)
          ) {
            if (
              ((b = Np),
              (w = "onMouseLeave"),
              (g = "onMouseEnter"),
              (p = "mouse"),
              (e === "pointerout" || e === "pointerover") &&
                ((b = Ap),
                (w = "onPointerLeave"),
                (g = "onPointerEnter"),
                (p = "pointer")),
              (j = v == null ? m : as(v)),
              (y = x == null ? m : as(x)),
              (m = new b(w, p + "leave", v, r, d)),
              (m.target = j),
              (m.relatedTarget = y),
              (w = null),
              pn(d) === u &&
                ((b = new b(g, p + "enter", x, r, d)),
                (b.target = y),
                (b.relatedTarget = j),
                (w = b)),
              (j = w),
              v && x)
            )
              t: {
                for (b = v, g = x, p = 0, y = b; y; y = ns(y)) p++;
                for (y = 0, w = g; w; w = ns(w)) y++;
                for (; 0 < p - y; ) ((b = ns(b)), p--);
                for (; 0 < y - p; ) ((g = ns(g)), y--);
                for (; p--; ) {
                  if (b === g || (g !== null && b === g.alternate)) break t;
                  ((b = ns(b)), (g = ns(g)));
                }
                b = null;
              }
            else b = null;
            (v !== null && rm(f, m, v, b, !1),
              x !== null && j !== null && rm(f, j, x, b, !0));
          }
        }
        e: {
          if (
            ((m = u ? as(u) : window),
            (v = m.nodeName && m.nodeName.toLowerCase()),
            v === "select" || (v === "input" && m.type === "file"))
          )
            var S = Y0;
          else if (Lp(m))
            if (zp) S = tv;
            else {
              S = X0;
              var A = J0;
            }
          else
            (v = m.nodeName) &&
              v.toLowerCase() === "input" &&
              (m.type === "checkbox" || m.type === "radio") &&
              (S = ev);
          if (S && (S = S(e, u))) {
            Dp(f, S, r, d);
            break e;
          }
          (A && A(e, m, u),
            e === "focusout" &&
              (A = m._wrapperState) &&
              A.controlled &&
              m.type === "number" &&
              hl(m, "number", m.value));
        }
        switch (((A = u ? as(u) : window), e)) {
          case "focusin":
            (Lp(A) || A.contentEditable === "true") &&
              ((ts = A), (ql = u), (fa = null));
            break;
          case "focusout":
            fa = ql = ts = null;
            break;
          case "mousedown":
            Vl = !0;
            break;
          case "contextmenu":
          case "mouseup":
          case "dragend":
            ((Vl = !1), Vp(f, r, d));
            break;
          case "selectionchange":
            if (sv) break;
          case "keydown":
          case "keyup":
            Vp(f, r, d);
        }
        var N;
        if (Fl)
          e: {
            switch (e) {
              case "compositionstart":
                var T = "onCompositionStart";
                break e;
              case "compositionend":
                T = "onCompositionEnd";
                break e;
              case "compositionupdate":
                T = "onCompositionUpdate";
                break e;
            }
            T = void 0;
          }
        else
          es
            ? Op(e, r) && (T = "onCompositionEnd")
            : e === "keydown" &&
              r.keyCode === 229 &&
              (T = "onCompositionStart");
        (T &&
          (Ep &&
            r.locale !== "ko" &&
            (es || T !== "onCompositionStart"
              ? T === "onCompositionEnd" && es && (N = Cp())
              : ((Tr = d),
                (Il = "value" in Tr ? Tr.value : Tr.textContent),
                (es = !0))),
          (A = Ro(u, T)),
          0 < A.length &&
            ((T = new Rp(T, e, null, r, d)),
            f.push({ event: T, listeners: A }),
            N ? (T.data = N) : ((N = Ip(r)), N !== null && (T.data = N)))),
          (N = K0 ? W0(e, r) : H0(e, r)) &&
            ((u = Ro(u, "onBeforeInput")),
            0 < u.length &&
              ((d = new Rp("onBeforeInput", "beforeinput", null, r, d)),
              f.push({ event: d, listeners: u }),
              (d.data = N))));
      }
      em(f, t);
    });
  }
  function ya(e, t, r) {
    return { instance: e, listener: t, currentTarget: r };
  }
  function Ro(e, t) {
    for (var r = t + "Capture", n = []; e !== null; ) {
      var s = e,
        a = s.stateNode;
      (s.tag === 5 &&
        a !== null &&
        ((s = a),
        (a = Js(e, r)),
        a != null && n.unshift(ya(e, a, s)),
        (a = Js(e, t)),
        a != null && n.push(ya(e, a, s))),
        (e = e.return));
    }
    return n;
  }
  function ns(e) {
    if (e === null) return null;
    do e = e.return;
    while (e && e.tag !== 5);
    return e || null;
  }
  function rm(e, t, r, n, s) {
    for (var a = t._reactName, o = []; r !== null && r !== n; ) {
      var l = r,
        c = l.alternate,
        u = l.stateNode;
      if (c !== null && c === n) break;
      (l.tag === 5 &&
        u !== null &&
        ((l = u),
        s
          ? ((c = Js(r, a)), c != null && o.unshift(ya(r, c, l)))
          : s || ((c = Js(r, a)), c != null && o.push(ya(r, c, l)))),
        (r = r.return));
    }
    o.length !== 0 && e.push({ event: t, listeners: o });
  }
  var lv = /\r\n?/g,
    cv = /\u0000|\uFFFD/g;
  function nm(e) {
    return (typeof e == "string" ? e : "" + e)
      .replace(
        lv,
        `
`
      )
      .replace(cv, "");
  }
  function Ao(e, t, r) {
    if (((t = nm(t)), nm(e) !== t && r)) throw Error(E(425));
  }
  function Eo() {}
  var Yl = null,
    Jl = null;
  function Xl(e, t) {
    return (
      e === "textarea" ||
      e === "noscript" ||
      typeof t.children == "string" ||
      typeof t.children == "number" ||
      (typeof t.dangerouslySetInnerHTML == "object" &&
        t.dangerouslySetInnerHTML !== null &&
        t.dangerouslySetInnerHTML.__html != null)
    );
  }
  var ec = typeof setTimeout == "function" ? setTimeout : void 0,
    uv = typeof clearTimeout == "function" ? clearTimeout : void 0,
    sm = typeof Promise == "function" ? Promise : void 0,
    dv =
      typeof queueMicrotask == "function"
        ? queueMicrotask
        : typeof sm < "u"
          ? function (e) {
              return sm.resolve(null).then(e).catch(pv);
            }
          : ec;
  function pv(e) {
    setTimeout(function () {
      throw e;
    });
  }
  function tc(e, t) {
    var r = t,
      n = 0;
    do {
      var s = r.nextSibling;
      if ((e.removeChild(r), s && s.nodeType === 8))
        if (((r = s.data), r === "/$")) {
          if (n === 0) {
            (e.removeChild(s), ia(t));
            return;
          }
          n--;
        } else (r !== "$" && r !== "$?" && r !== "$!") || n++;
      r = s;
    } while (r);
    ia(t);
  }
  function Or(e) {
    for (; e != null; e = e.nextSibling) {
      var t = e.nodeType;
      if (t === 1 || t === 3) break;
      if (t === 8) {
        if (((t = e.data), t === "$" || t === "$!" || t === "$?")) break;
        if (t === "/$") return null;
      }
    }
    return e;
  }
  function am(e) {
    e = e.previousSibling;
    for (var t = 0; e; ) {
      if (e.nodeType === 8) {
        var r = e.data;
        if (r === "$" || r === "$!" || r === "$?") {
          if (t === 0) return e;
          t--;
        } else r === "/$" && t++;
      }
      e = e.previousSibling;
    }
    return null;
  }
  var ss = Math.random().toString(36).slice(2),
    Ht = "__reactFiber$" + ss,
    va = "__reactProps$" + ss,
    or = "__reactContainer$" + ss,
    rc = "__reactEvents$" + ss,
    mv = "__reactListeners$" + ss,
    fv = "__reactHandles$" + ss;
  function pn(e) {
    var t = e[Ht];
    if (t) return t;
    for (var r = e.parentNode; r; ) {
      if ((t = r[or] || r[Ht])) {
        if (
          ((r = t.alternate),
          t.child !== null || (r !== null && r.child !== null))
        )
          for (e = am(e); e !== null; ) {
            if ((r = e[Ht])) return r;
            e = am(e);
          }
        return t;
      }
      ((e = r), (r = e.parentNode));
    }
    return null;
  }
  function xa(e) {
    return (
      (e = e[Ht] || e[or]),
      !e || (e.tag !== 5 && e.tag !== 6 && e.tag !== 13 && e.tag !== 3)
        ? null
        : e
    );
  }
  function as(e) {
    if (e.tag === 5 || e.tag === 6) return e.stateNode;
    throw Error(E(33));
  }
  function To(e) {
    return e[va] || null;
  }
  var nc = [],
    os = -1;
  function Ir(e) {
    return { current: e };
  }
  function ge(e) {
    0 > os || ((e.current = nc[os]), (nc[os] = null), os--);
  }
  function me(e, t) {
    (os++, (nc[os] = e.current), (e.current = t));
  }
  var Lr = {},
    Qe = Ir(Lr),
    lt = Ir(!1),
    mn = Lr;
  function is(e, t) {
    var r = e.type.contextTypes;
    if (!r) return Lr;
    var n = e.stateNode;
    if (n && n.__reactInternalMemoizedUnmaskedChildContext === t)
      return n.__reactInternalMemoizedMaskedChildContext;
    var s = {},
      a;
    for (a in r) s[a] = t[a];
    return (
      n &&
        ((e = e.stateNode),
        (e.__reactInternalMemoizedUnmaskedChildContext = t),
        (e.__reactInternalMemoizedMaskedChildContext = s)),
      s
    );
  }
  function ct(e) {
    return ((e = e.childContextTypes), e != null);
  }
  function Po() {
    (ge(lt), ge(Qe));
  }
  function om(e, t, r) {
    if (Qe.current !== Lr) throw Error(E(168));
    (me(Qe, t), me(lt, r));
  }
  function im(e, t, r) {
    var n = e.stateNode;
    if (((t = t.childContextTypes), typeof n.getChildContext != "function"))
      return r;
    n = n.getChildContext();
    for (var s in n) if (!(s in t)) throw Error(E(108, Jy(e) || "Unknown", s));
    return be({}, r, n);
  }
  function Oo(e) {
    return (
      (e =
        ((e = e.stateNode) && e.__reactInternalMemoizedMergedChildContext) ||
        Lr),
      (mn = Qe.current),
      me(Qe, e),
      me(lt, lt.current),
      !0
    );
  }
  function lm(e, t, r) {
    var n = e.stateNode;
    if (!n) throw Error(E(169));
    (r
      ? ((e = im(e, t, mn)),
        (n.__reactInternalMemoizedMergedChildContext = e),
        ge(lt),
        ge(Qe),
        me(Qe, e))
      : ge(lt),
      me(lt, r));
  }
  var ir = null,
    Io = !1,
    sc = !1;
  function cm(e) {
    ir === null ? (ir = [e]) : ir.push(e);
  }
  function hv(e) {
    ((Io = !0), cm(e));
  }
  function Dr() {
    if (!sc && ir !== null) {
      sc = !0;
      var e = 0,
        t = ce;
      try {
        var r = ir;
        for (ce = 1; e < r.length; e++) {
          var n = r[e];
          do n = n(!0);
          while (n !== null);
        }
        ((ir = null), (Io = !1));
      } catch (s) {
        throw (ir !== null && (ir = ir.slice(e + 1)), dp(jl, Dr), s);
      } finally {
        ((ce = t), (sc = !1));
      }
    }
    return null;
  }
  var ls = [],
    cs = 0,
    Lo = null,
    Do = 0,
    Ct = [],
    jt = 0,
    fn = null,
    lr = 1,
    cr = "";
  function hn(e, t) {
    ((ls[cs++] = Do), (ls[cs++] = Lo), (Lo = e), (Do = t));
  }
  function um(e, t, r) {
    ((Ct[jt++] = lr), (Ct[jt++] = cr), (Ct[jt++] = fn), (fn = e));
    var n = lr;
    e = cr;
    var s = 32 - Dt(n) - 1;
    ((n &= ~(1 << s)), (r += 1));
    var a = 32 - Dt(t) + s;
    if (30 < a) {
      var o = s - (s % 5);
      ((a = (n & ((1 << o) - 1)).toString(32)),
        (n >>= o),
        (s -= o),
        (lr = (1 << (32 - Dt(t) + s)) | (r << s) | n),
        (cr = a + e));
    } else ((lr = (1 << a) | (r << s) | n), (cr = e));
  }
  function ac(e) {
    e.return !== null && (hn(e, 1), um(e, 1, 0));
  }
  function oc(e) {
    for (; e === Lo; )
      ((Lo = ls[--cs]), (ls[cs] = null), (Do = ls[--cs]), (ls[cs] = null));
    for (; e === fn; )
      ((fn = Ct[--jt]),
        (Ct[jt] = null),
        (cr = Ct[--jt]),
        (Ct[jt] = null),
        (lr = Ct[--jt]),
        (Ct[jt] = null));
  }
  var bt = null,
    wt = null,
    ye = !1,
    Mt = null;
  function dm(e, t) {
    var r = Et(5, null, null, 0);
    ((r.elementType = "DELETED"),
      (r.stateNode = t),
      (r.return = e),
      (t = e.deletions),
      t === null ? ((e.deletions = [r]), (e.flags |= 16)) : t.push(r));
  }
  function pm(e, t) {
    switch (e.tag) {
      case 5:
        var r = e.type;
        return (
          (t =
            t.nodeType !== 1 || r.toLowerCase() !== t.nodeName.toLowerCase()
              ? null
              : t),
          t !== null
            ? ((e.stateNode = t), (bt = e), (wt = Or(t.firstChild)), !0)
            : !1
        );
      case 6:
        return (
          (t = e.pendingProps === "" || t.nodeType !== 3 ? null : t),
          t !== null ? ((e.stateNode = t), (bt = e), (wt = null), !0) : !1
        );
      case 13:
        return (
          (t = t.nodeType !== 8 ? null : t),
          t !== null
            ? ((r = fn !== null ? { id: lr, overflow: cr } : null),
              (e.memoizedState = {
                dehydrated: t,
                treeContext: r,
                retryLane: 1073741824,
              }),
              (r = Et(18, null, null, 0)),
              (r.stateNode = t),
              (r.return = e),
              (e.child = r),
              (bt = e),
              (wt = null),
              !0)
            : !1
        );
      default:
        return !1;
    }
  }
  function ic(e) {
    return (e.mode & 1) !== 0 && (e.flags & 128) === 0;
  }
  function lc(e) {
    if (ye) {
      var t = wt;
      if (t) {
        var r = t;
        if (!pm(e, t)) {
          if (ic(e)) throw Error(E(418));
          t = Or(r.nextSibling);
          var n = bt;
          t && pm(e, t)
            ? dm(n, r)
            : ((e.flags = (e.flags & -4097) | 2), (ye = !1), (bt = e));
        }
      } else {
        if (ic(e)) throw Error(E(418));
        ((e.flags = (e.flags & -4097) | 2), (ye = !1), (bt = e));
      }
    }
  }
  function mm(e) {
    for (
      e = e.return;
      e !== null && e.tag !== 5 && e.tag !== 3 && e.tag !== 13;
    )
      e = e.return;
    bt = e;
  }
  function zo(e) {
    if (e !== bt) return !1;
    if (!ye) return (mm(e), (ye = !0), !1);
    var t;
    if (
      ((t = e.tag !== 3) &&
        !(t = e.tag !== 5) &&
        ((t = e.type),
        (t = t !== "head" && t !== "body" && !Xl(e.type, e.memoizedProps))),
      t && (t = wt))
    ) {
      if (ic(e)) throw (fm(), Error(E(418)));
      for (; t; ) (dm(e, t), (t = Or(t.nextSibling)));
    }
    if ((mm(e), e.tag === 13)) {
      if (((e = e.memoizedState), (e = e !== null ? e.dehydrated : null), !e))
        throw Error(E(317));
      e: {
        for (e = e.nextSibling, t = 0; e; ) {
          if (e.nodeType === 8) {
            var r = e.data;
            if (r === "/$") {
              if (t === 0) {
                wt = Or(e.nextSibling);
                break e;
              }
              t--;
            } else (r !== "$" && r !== "$!" && r !== "$?") || t++;
          }
          e = e.nextSibling;
        }
        wt = null;
      }
    } else wt = bt ? Or(e.stateNode.nextSibling) : null;
    return !0;
  }
  function fm() {
    for (var e = wt; e; ) e = Or(e.nextSibling);
  }
  function us() {
    ((wt = bt = null), (ye = !1));
  }
  function cc(e) {
    Mt === null ? (Mt = [e]) : Mt.push(e);
  }
  var gv = ar.ReactCurrentBatchConfig;
  function ba(e, t, r) {
    if (
      ((e = r.ref),
      e !== null && typeof e != "function" && typeof e != "object")
    ) {
      if (r._owner) {
        if (((r = r._owner), r)) {
          if (r.tag !== 1) throw Error(E(309));
          var n = r.stateNode;
        }
        if (!n) throw Error(E(147, e));
        var s = n,
          a = "" + e;
        return t !== null &&
          t.ref !== null &&
          typeof t.ref == "function" &&
          t.ref._stringRef === a
          ? t.ref
          : ((t = function (o) {
              var l = s.refs;
              o === null ? delete l[a] : (l[a] = o);
            }),
            (t._stringRef = a),
            t);
      }
      if (typeof e != "string") throw Error(E(284));
      if (!r._owner) throw Error(E(290, e));
    }
    return e;
  }
  function Mo(e, t) {
    throw (
      (e = Object.prototype.toString.call(t)),
      Error(
        E(
          31,
          e === "[object Object]"
            ? "object with keys {" + Object.keys(t).join(", ") + "}"
            : e
        )
      )
    );
  }
  function hm(e) {
    var t = e._init;
    return t(e._payload);
  }
  function gm(e) {
    function t(g, p) {
      if (e) {
        var y = g.deletions;
        y === null ? ((g.deletions = [p]), (g.flags |= 16)) : y.push(p);
      }
    }
    function r(g, p) {
      if (!e) return null;
      for (; p !== null; ) (t(g, p), (p = p.sibling));
      return null;
    }
    function n(g, p) {
      for (g = new Map(); p !== null; )
        (p.key !== null ? g.set(p.key, p) : g.set(p.index, p), (p = p.sibling));
      return g;
    }
    function s(g, p) {
      return ((g = qr(g, p)), (g.index = 0), (g.sibling = null), g);
    }
    function a(g, p, y) {
      return (
        (g.index = y),
        e
          ? ((y = g.alternate),
            y !== null
              ? ((y = y.index), y < p ? ((g.flags |= 2), p) : y)
              : ((g.flags |= 2), p))
          : ((g.flags |= 1048576), p)
      );
    }
    function o(g) {
      return (e && g.alternate === null && (g.flags |= 2), g);
    }
    function l(g, p, y, w) {
      return p === null || p.tag !== 6
        ? ((p = eu(y, g.mode, w)), (p.return = g), p)
        : ((p = s(p, y)), (p.return = g), p);
    }
    function c(g, p, y, w) {
      var S = y.type;
      return S === Hn
        ? d(g, p, y.props.children, w, y.key)
        : p !== null &&
            (p.elementType === S ||
              (typeof S == "object" &&
                S !== null &&
                S.$$typeof === Cr &&
                hm(S) === p.type))
          ? ((w = s(p, y.props)), (w.ref = ba(g, p, y)), (w.return = g), w)
          : ((w = li(y.type, y.key, y.props, null, g.mode, w)),
            (w.ref = ba(g, p, y)),
            (w.return = g),
            w);
    }
    function u(g, p, y, w) {
      return p === null ||
        p.tag !== 4 ||
        p.stateNode.containerInfo !== y.containerInfo ||
        p.stateNode.implementation !== y.implementation
        ? ((p = tu(y, g.mode, w)), (p.return = g), p)
        : ((p = s(p, y.children || [])), (p.return = g), p);
    }
    function d(g, p, y, w, S) {
      return p === null || p.tag !== 7
        ? ((p = _n(y, g.mode, w, S)), (p.return = g), p)
        : ((p = s(p, y)), (p.return = g), p);
    }
    function f(g, p, y) {
      if ((typeof p == "string" && p !== "") || typeof p == "number")
        return ((p = eu("" + p, g.mode, y)), (p.return = g), p);
      if (typeof p == "object" && p !== null) {
        switch (p.$$typeof) {
          case so:
            return (
              (y = li(p.type, p.key, p.props, null, g.mode, y)),
              (y.ref = ba(g, null, p)),
              (y.return = g),
              y
            );
          case Wn:
            return ((p = tu(p, g.mode, y)), (p.return = g), p);
          case Cr:
            var w = p._init;
            return f(g, w(p._payload), y);
        }
        if (Qs(p) || Ws(p))
          return ((p = _n(p, g.mode, y, null)), (p.return = g), p);
        Mo(g, p);
      }
      return null;
    }
    function m(g, p, y, w) {
      var S = p !== null ? p.key : null;
      if ((typeof y == "string" && y !== "") || typeof y == "number")
        return S !== null ? null : l(g, p, "" + y, w);
      if (typeof y == "object" && y !== null) {
        switch (y.$$typeof) {
          case so:
            return y.key === S ? c(g, p, y, w) : null;
          case Wn:
            return y.key === S ? u(g, p, y, w) : null;
          case Cr:
            return ((S = y._init), m(g, p, S(y._payload), w));
        }
        if (Qs(y) || Ws(y)) return S !== null ? null : d(g, p, y, w, null);
        Mo(g, y);
      }
      return null;
    }
    function v(g, p, y, w, S) {
      if ((typeof w == "string" && w !== "") || typeof w == "number")
        return ((g = g.get(y) || null), l(p, g, "" + w, S));
      if (typeof w == "object" && w !== null) {
        switch (w.$$typeof) {
          case so:
            return (
              (g = g.get(w.key === null ? y : w.key) || null),
              c(p, g, w, S)
            );
          case Wn:
            return (
              (g = g.get(w.key === null ? y : w.key) || null),
              u(p, g, w, S)
            );
          case Cr:
            var A = w._init;
            return v(g, p, y, A(w._payload), S);
        }
        if (Qs(w) || Ws(w))
          return ((g = g.get(y) || null), d(p, g, w, S, null));
        Mo(p, w);
      }
      return null;
    }
    function x(g, p, y, w) {
      for (
        var S = null, A = null, N = p, T = (p = 0), _ = null;
        N !== null && T < y.length;
        T++
      ) {
        N.index > T ? ((_ = N), (N = null)) : (_ = N.sibling);
        var O = m(g, N, y[T], w);
        if (O === null) {
          N === null && (N = _);
          break;
        }
        (e && N && O.alternate === null && t(g, N),
          (p = a(O, p, T)),
          A === null ? (S = O) : (A.sibling = O),
          (A = O),
          (N = _));
      }
      if (T === y.length) return (r(g, N), ye && hn(g, T), S);
      if (N === null) {
        for (; T < y.length; T++)
          ((N = f(g, y[T], w)),
            N !== null &&
              ((p = a(N, p, T)),
              A === null ? (S = N) : (A.sibling = N),
              (A = N)));
        return (ye && hn(g, T), S);
      }
      for (N = n(g, N); T < y.length; T++)
        ((_ = v(N, g, T, y[T], w)),
          _ !== null &&
            (e && _.alternate !== null && N.delete(_.key === null ? T : _.key),
            (p = a(_, p, T)),
            A === null ? (S = _) : (A.sibling = _),
            (A = _)));
      return (
        e &&
          N.forEach(function (q) {
            return t(g, q);
          }),
        ye && hn(g, T),
        S
      );
    }
    function b(g, p, y, w) {
      var S = Ws(y);
      if (typeof S != "function") throw Error(E(150));
      if (((y = S.call(y)), y == null)) throw Error(E(151));
      for (
        var A = (S = null), N = p, T = (p = 0), _ = null, O = y.next();
        N !== null && !O.done;
        T++, O = y.next()
      ) {
        N.index > T ? ((_ = N), (N = null)) : (_ = N.sibling);
        var q = m(g, N, O.value, w);
        if (q === null) {
          N === null && (N = _);
          break;
        }
        (e && N && q.alternate === null && t(g, N),
          (p = a(q, p, T)),
          A === null ? (S = q) : (A.sibling = q),
          (A = q),
          (N = _));
      }
      if (O.done) return (r(g, N), ye && hn(g, T), S);
      if (N === null) {
        for (; !O.done; T++, O = y.next())
          ((O = f(g, O.value, w)),
            O !== null &&
              ((p = a(O, p, T)),
              A === null ? (S = O) : (A.sibling = O),
              (A = O)));
        return (ye && hn(g, T), S);
      }
      for (N = n(g, N); !O.done; T++, O = y.next())
        ((O = v(N, g, T, O.value, w)),
          O !== null &&
            (e && O.alternate !== null && N.delete(O.key === null ? T : O.key),
            (p = a(O, p, T)),
            A === null ? (S = O) : (A.sibling = O),
            (A = O)));
      return (
        e &&
          N.forEach(function (ee) {
            return t(g, ee);
          }),
        ye && hn(g, T),
        S
      );
    }
    function j(g, p, y, w) {
      if (
        (typeof y == "object" &&
          y !== null &&
          y.type === Hn &&
          y.key === null &&
          (y = y.props.children),
        typeof y == "object" && y !== null)
      ) {
        switch (y.$$typeof) {
          case so:
            e: {
              for (var S = y.key, A = p; A !== null; ) {
                if (A.key === S) {
                  if (((S = y.type), S === Hn)) {
                    if (A.tag === 7) {
                      (r(g, A.sibling),
                        (p = s(A, y.props.children)),
                        (p.return = g),
                        (g = p));
                      break e;
                    }
                  } else if (
                    A.elementType === S ||
                    (typeof S == "object" &&
                      S !== null &&
                      S.$$typeof === Cr &&
                      hm(S) === A.type)
                  ) {
                    (r(g, A.sibling),
                      (p = s(A, y.props)),
                      (p.ref = ba(g, A, y)),
                      (p.return = g),
                      (g = p));
                    break e;
                  }
                  r(g, A);
                  break;
                } else t(g, A);
                A = A.sibling;
              }
              y.type === Hn
                ? ((p = _n(y.props.children, g.mode, w, y.key)),
                  (p.return = g),
                  (g = p))
                : ((w = li(y.type, y.key, y.props, null, g.mode, w)),
                  (w.ref = ba(g, p, y)),
                  (w.return = g),
                  (g = w));
            }
            return o(g);
          case Wn:
            e: {
              for (A = y.key; p !== null; ) {
                if (p.key === A)
                  if (
                    p.tag === 4 &&
                    p.stateNode.containerInfo === y.containerInfo &&
                    p.stateNode.implementation === y.implementation
                  ) {
                    (r(g, p.sibling),
                      (p = s(p, y.children || [])),
                      (p.return = g),
                      (g = p));
                    break e;
                  } else {
                    r(g, p);
                    break;
                  }
                else t(g, p);
                p = p.sibling;
              }
              ((p = tu(y, g.mode, w)), (p.return = g), (g = p));
            }
            return o(g);
          case Cr:
            return ((A = y._init), j(g, p, A(y._payload), w));
        }
        if (Qs(y)) return x(g, p, y, w);
        if (Ws(y)) return b(g, p, y, w);
        Mo(g, y);
      }
      return (typeof y == "string" && y !== "") || typeof y == "number"
        ? ((y = "" + y),
          p !== null && p.tag === 6
            ? (r(g, p.sibling), (p = s(p, y)), (p.return = g), (g = p))
            : (r(g, p), (p = eu(y, g.mode, w)), (p.return = g), (g = p)),
          o(g))
        : r(g, p);
    }
    return j;
  }
  var ds = gm(!0),
    ym = gm(!1),
    $o = Ir(null),
    Fo = null,
    ps = null,
    uc = null;
  function dc() {
    uc = ps = Fo = null;
  }
  function pc(e) {
    var t = $o.current;
    (ge($o), (e._currentValue = t));
  }
  function mc(e, t, r) {
    for (; e !== null; ) {
      var n = e.alternate;
      if (
        ((e.childLanes & t) !== t
          ? ((e.childLanes |= t), n !== null && (n.childLanes |= t))
          : n !== null && (n.childLanes & t) !== t && (n.childLanes |= t),
        e === r)
      )
        break;
      e = e.return;
    }
  }
  function ms(e, t) {
    ((Fo = e),
      (uc = ps = null),
      (e = e.dependencies),
      e !== null &&
        e.firstContext !== null &&
        ((e.lanes & t) !== 0 && (ut = !0), (e.firstContext = null)));
  }
  function Nt(e) {
    var t = e._currentValue;
    if (uc !== e)
      if (((e = { context: e, memoizedValue: t, next: null }), ps === null)) {
        if (Fo === null) throw Error(E(308));
        ((ps = e), (Fo.dependencies = { lanes: 0, firstContext: e }));
      } else ps = ps.next = e;
    return t;
  }
  var gn = null;
  function fc(e) {
    gn === null ? (gn = [e]) : gn.push(e);
  }
  function vm(e, t, r, n) {
    var s = t.interleaved;
    return (
      s === null ? ((r.next = r), fc(t)) : ((r.next = s.next), (s.next = r)),
      (t.interleaved = r),
      ur(e, n)
    );
  }
  function ur(e, t) {
    e.lanes |= t;
    var r = e.alternate;
    for (r !== null && (r.lanes |= t), r = e, e = e.return; e !== null; )
      ((e.childLanes |= t),
        (r = e.alternate),
        r !== null && (r.childLanes |= t),
        (r = e),
        (e = e.return));
    return r.tag === 3 ? r.stateNode : null;
  }
  var zr = !1;
  function hc(e) {
    e.updateQueue = {
      baseState: e.memoizedState,
      firstBaseUpdate: null,
      lastBaseUpdate: null,
      shared: { pending: null, interleaved: null, lanes: 0 },
      effects: null,
    };
  }
  function xm(e, t) {
    ((e = e.updateQueue),
      t.updateQueue === e &&
        (t.updateQueue = {
          baseState: e.baseState,
          firstBaseUpdate: e.firstBaseUpdate,
          lastBaseUpdate: e.lastBaseUpdate,
          shared: e.shared,
          effects: e.effects,
        }));
  }
  function dr(e, t) {
    return {
      eventTime: e,
      lane: t,
      tag: 0,
      payload: null,
      callback: null,
      next: null,
    };
  }
  function Mr(e, t, r) {
    var n = e.updateQueue;
    if (n === null) return null;
    if (((n = n.shared), (ne & 2) !== 0)) {
      var s = n.pending;
      return (
        s === null ? (t.next = t) : ((t.next = s.next), (s.next = t)),
        (n.pending = t),
        ur(e, r)
      );
    }
    return (
      (s = n.interleaved),
      s === null ? ((t.next = t), fc(n)) : ((t.next = s.next), (s.next = t)),
      (n.interleaved = t),
      ur(e, r)
    );
  }
  function Uo(e, t, r) {
    if (
      ((t = t.updateQueue), t !== null && ((t = t.shared), (r & 4194240) !== 0))
    ) {
      var n = t.lanes;
      ((n &= e.pendingLanes), (r |= n), (t.lanes = r), Al(e, r));
    }
  }
  function bm(e, t) {
    var r = e.updateQueue,
      n = e.alternate;
    if (n !== null && ((n = n.updateQueue), r === n)) {
      var s = null,
        a = null;
      if (((r = r.firstBaseUpdate), r !== null)) {
        do {
          var o = {
            eventTime: r.eventTime,
            lane: r.lane,
            tag: r.tag,
            payload: r.payload,
            callback: r.callback,
            next: null,
          };
          (a === null ? (s = a = o) : (a = a.next = o), (r = r.next));
        } while (r !== null);
        a === null ? (s = a = t) : (a = a.next = t);
      } else s = a = t;
      ((r = {
        baseState: n.baseState,
        firstBaseUpdate: s,
        lastBaseUpdate: a,
        shared: n.shared,
        effects: n.effects,
      }),
        (e.updateQueue = r));
      return;
    }
    ((e = r.lastBaseUpdate),
      e === null ? (r.firstBaseUpdate = t) : (e.next = t),
      (r.lastBaseUpdate = t));
  }
  function Zo(e, t, r, n) {
    var s = e.updateQueue;
    zr = !1;
    var a = s.firstBaseUpdate,
      o = s.lastBaseUpdate,
      l = s.shared.pending;
    if (l !== null) {
      s.shared.pending = null;
      var c = l,
        u = c.next;
      ((c.next = null), o === null ? (a = u) : (o.next = u), (o = c));
      var d = e.alternate;
      d !== null &&
        ((d = d.updateQueue),
        (l = d.lastBaseUpdate),
        l !== o &&
          (l === null ? (d.firstBaseUpdate = u) : (l.next = u),
          (d.lastBaseUpdate = c)));
    }
    if (a !== null) {
      var f = s.baseState;
      ((o = 0), (d = u = c = null), (l = a));
      do {
        var m = l.lane,
          v = l.eventTime;
        if ((n & m) === m) {
          d !== null &&
            (d = d.next =
              {
                eventTime: v,
                lane: 0,
                tag: l.tag,
                payload: l.payload,
                callback: l.callback,
                next: null,
              });
          e: {
            var x = e,
              b = l;
            switch (((m = t), (v = r), b.tag)) {
              case 1:
                if (((x = b.payload), typeof x == "function")) {
                  f = x.call(v, f, m);
                  break e;
                }
                f = x;
                break e;
              case 3:
                x.flags = (x.flags & -65537) | 128;
              case 0:
                if (
                  ((x = b.payload),
                  (m = typeof x == "function" ? x.call(v, f, m) : x),
                  m == null)
                )
                  break e;
                f = be({}, f, m);
                break e;
              case 2:
                zr = !0;
            }
          }
          l.callback !== null &&
            l.lane !== 0 &&
            ((e.flags |= 64),
            (m = s.effects),
            m === null ? (s.effects = [l]) : m.push(l));
        } else
          ((v = {
            eventTime: v,
            lane: m,
            tag: l.tag,
            payload: l.payload,
            callback: l.callback,
            next: null,
          }),
            d === null ? ((u = d = v), (c = f)) : (d = d.next = v),
            (o |= m));
        if (((l = l.next), l === null)) {
          if (((l = s.shared.pending), l === null)) break;
          ((m = l),
            (l = m.next),
            (m.next = null),
            (s.lastBaseUpdate = m),
            (s.shared.pending = null));
        }
      } while (!0);
      if (
        (d === null && (c = f),
        (s.baseState = c),
        (s.firstBaseUpdate = u),
        (s.lastBaseUpdate = d),
        (t = s.shared.interleaved),
        t !== null)
      ) {
        s = t;
        do ((o |= s.lane), (s = s.next));
        while (s !== t);
      } else a === null && (s.shared.lanes = 0);
      ((xn |= o), (e.lanes = o), (e.memoizedState = f));
    }
  }
  function wm(e, t, r) {
    if (((e = t.effects), (t.effects = null), e !== null))
      for (t = 0; t < e.length; t++) {
        var n = e[t],
          s = n.callback;
        if (s !== null) {
          if (((n.callback = null), (n = r), typeof s != "function"))
            throw Error(E(191, s));
          s.call(n);
        }
      }
  }
  var wa = {},
    Qt = Ir(wa),
    ka = Ir(wa),
    _a = Ir(wa);
  function yn(e) {
    if (e === wa) throw Error(E(174));
    return e;
  }
  function gc(e, t) {
    switch ((me(_a, t), me(ka, e), me(Qt, wa), (e = t.nodeType), e)) {
      case 9:
      case 11:
        t = (t = t.documentElement) ? t.namespaceURI : yl(null, "");
        break;
      default:
        ((e = e === 8 ? t.parentNode : t),
          (t = e.namespaceURI || null),
          (e = e.tagName),
          (t = yl(t, e)));
    }
    (ge(Qt), me(Qt, t));
  }
  function fs() {
    (ge(Qt), ge(ka), ge(_a));
  }
  function km(e) {
    yn(_a.current);
    var t = yn(Qt.current),
      r = yl(t, e.type);
    t !== r && (me(ka, e), me(Qt, r));
  }
  function yc(e) {
    ka.current === e && (ge(Qt), ge(ka));
  }
  var we = Ir(0);
  function Bo(e) {
    for (var t = e; t !== null; ) {
      if (t.tag === 13) {
        var r = t.memoizedState;
        if (
          r !== null &&
          ((r = r.dehydrated), r === null || r.data === "$?" || r.data === "$!")
        )
          return t;
      } else if (t.tag === 19 && t.memoizedProps.revealOrder !== void 0) {
        if ((t.flags & 128) !== 0) return t;
      } else if (t.child !== null) {
        ((t.child.return = t), (t = t.child));
        continue;
      }
      if (t === e) break;
      for (; t.sibling === null; ) {
        if (t.return === null || t.return === e) return null;
        t = t.return;
      }
      ((t.sibling.return = t.return), (t = t.sibling));
    }
    return null;
  }
  var vc = [];
  function xc() {
    for (var e = 0; e < vc.length; e++)
      vc[e]._workInProgressVersionPrimary = null;
    vc.length = 0;
  }
  var qo = ar.ReactCurrentDispatcher,
    bc = ar.ReactCurrentBatchConfig,
    vn = 0,
    ke = null,
    ze = null,
    Ue = null,
    Vo = !1,
    Sa = !1,
    Ca = 0,
    yv = 0;
  function Ge() {
    throw Error(E(321));
  }
  function wc(e, t) {
    if (t === null) return !1;
    for (var r = 0; r < t.length && r < e.length; r++)
      if (!zt(e[r], t[r])) return !1;
    return !0;
  }
  function kc(e, t, r, n, s, a) {
    if (
      ((vn = a),
      (ke = t),
      (t.memoizedState = null),
      (t.updateQueue = null),
      (t.lanes = 0),
      (qo.current = e === null || e.memoizedState === null ? wv : kv),
      (e = r(n, s)),
      Sa)
    ) {
      a = 0;
      do {
        if (((Sa = !1), (Ca = 0), 25 <= a)) throw Error(E(301));
        ((a += 1),
          (Ue = ze = null),
          (t.updateQueue = null),
          (qo.current = _v),
          (e = r(n, s)));
      } while (Sa);
    }
    if (
      ((qo.current = Ho),
      (t = ze !== null && ze.next !== null),
      (vn = 0),
      (Ue = ze = ke = null),
      (Vo = !1),
      t)
    )
      throw Error(E(300));
    return e;
  }
  function _c() {
    var e = Ca !== 0;
    return ((Ca = 0), e);
  }
  function Gt() {
    var e = {
      memoizedState: null,
      baseState: null,
      baseQueue: null,
      queue: null,
      next: null,
    };
    return (Ue === null ? (ke.memoizedState = Ue = e) : (Ue = Ue.next = e), Ue);
  }
  function Rt() {
    if (ze === null) {
      var e = ke.alternate;
      e = e !== null ? e.memoizedState : null;
    } else e = ze.next;
    var t = Ue === null ? ke.memoizedState : Ue.next;
    if (t !== null) ((Ue = t), (ze = e));
    else {
      if (e === null) throw Error(E(310));
      ((ze = e),
        (e = {
          memoizedState: ze.memoizedState,
          baseState: ze.baseState,
          baseQueue: ze.baseQueue,
          queue: ze.queue,
          next: null,
        }),
        Ue === null ? (ke.memoizedState = Ue = e) : (Ue = Ue.next = e));
    }
    return Ue;
  }
  function ja(e, t) {
    return typeof t == "function" ? t(e) : t;
  }
  function Sc(e) {
    var t = Rt(),
      r = t.queue;
    if (r === null) throw Error(E(311));
    r.lastRenderedReducer = e;
    var n = ze,
      s = n.baseQueue,
      a = r.pending;
    if (a !== null) {
      if (s !== null) {
        var o = s.next;
        ((s.next = a.next), (a.next = o));
      }
      ((n.baseQueue = s = a), (r.pending = null));
    }
    if (s !== null) {
      ((a = s.next), (n = n.baseState));
      var l = (o = null),
        c = null,
        u = a;
      do {
        var d = u.lane;
        if ((vn & d) === d)
          (c !== null &&
            (c = c.next =
              {
                lane: 0,
                action: u.action,
                hasEagerState: u.hasEagerState,
                eagerState: u.eagerState,
                next: null,
              }),
            (n = u.hasEagerState ? u.eagerState : e(n, u.action)));
        else {
          var f = {
            lane: d,
            action: u.action,
            hasEagerState: u.hasEagerState,
            eagerState: u.eagerState,
            next: null,
          };
          (c === null ? ((l = c = f), (o = n)) : (c = c.next = f),
            (ke.lanes |= d),
            (xn |= d));
        }
        u = u.next;
      } while (u !== null && u !== a);
      (c === null ? (o = n) : (c.next = l),
        zt(n, t.memoizedState) || (ut = !0),
        (t.memoizedState = n),
        (t.baseState = o),
        (t.baseQueue = c),
        (r.lastRenderedState = n));
    }
    if (((e = r.interleaved), e !== null)) {
      s = e;
      do ((a = s.lane), (ke.lanes |= a), (xn |= a), (s = s.next));
      while (s !== e);
    } else s === null && (r.lanes = 0);
    return [t.memoizedState, r.dispatch];
  }
  function Cc(e) {
    var t = Rt(),
      r = t.queue;
    if (r === null) throw Error(E(311));
    r.lastRenderedReducer = e;
    var n = r.dispatch,
      s = r.pending,
      a = t.memoizedState;
    if (s !== null) {
      r.pending = null;
      var o = (s = s.next);
      do ((a = e(a, o.action)), (o = o.next));
      while (o !== s);
      (zt(a, t.memoizedState) || (ut = !0),
        (t.memoizedState = a),
        t.baseQueue === null && (t.baseState = a),
        (r.lastRenderedState = a));
    }
    return [a, n];
  }
  function _m() {}
  function Sm(e, t) {
    var r = ke,
      n = Rt(),
      s = t(),
      a = !zt(n.memoizedState, s);
    if (
      (a && ((n.memoizedState = s), (ut = !0)),
      (n = n.queue),
      jc(Nm.bind(null, r, n, e), [e]),
      n.getSnapshot !== t || a || (Ue !== null && Ue.memoizedState.tag & 1))
    ) {
      if (
        ((r.flags |= 2048),
        Na(9, jm.bind(null, r, n, s, t), void 0, null),
        Ze === null)
      )
        throw Error(E(349));
      (vn & 30) !== 0 || Cm(r, t, s);
    }
    return s;
  }
  function Cm(e, t, r) {
    ((e.flags |= 16384),
      (e = { getSnapshot: t, value: r }),
      (t = ke.updateQueue),
      t === null
        ? ((t = { lastEffect: null, stores: null }),
          (ke.updateQueue = t),
          (t.stores = [e]))
        : ((r = t.stores), r === null ? (t.stores = [e]) : r.push(e)));
  }
  function jm(e, t, r, n) {
    ((t.value = r), (t.getSnapshot = n), Rm(t) && Am(e));
  }
  function Nm(e, t, r) {
    return r(function () {
      Rm(t) && Am(e);
    });
  }
  function Rm(e) {
    var t = e.getSnapshot;
    e = e.value;
    try {
      var r = t();
      return !zt(e, r);
    } catch {
      return !0;
    }
  }
  function Am(e) {
    var t = ur(e, 1);
    t !== null && Zt(t, e, 1, -1);
  }
  function Em(e) {
    var t = Gt();
    return (
      typeof e == "function" && (e = e()),
      (t.memoizedState = t.baseState = e),
      (e = {
        pending: null,
        interleaved: null,
        lanes: 0,
        dispatch: null,
        lastRenderedReducer: ja,
        lastRenderedState: e,
      }),
      (t.queue = e),
      (e = e.dispatch = bv.bind(null, ke, e)),
      [t.memoizedState, e]
    );
  }
  function Na(e, t, r, n) {
    return (
      (e = { tag: e, create: t, destroy: r, deps: n, next: null }),
      (t = ke.updateQueue),
      t === null
        ? ((t = { lastEffect: null, stores: null }),
          (ke.updateQueue = t),
          (t.lastEffect = e.next = e))
        : ((r = t.lastEffect),
          r === null
            ? (t.lastEffect = e.next = e)
            : ((n = r.next), (r.next = e), (e.next = n), (t.lastEffect = e))),
      e
    );
  }
  function Tm() {
    return Rt().memoizedState;
  }
  function Ko(e, t, r, n) {
    var s = Gt();
    ((ke.flags |= e),
      (s.memoizedState = Na(1 | t, r, void 0, n === void 0 ? null : n)));
  }
  function Wo(e, t, r, n) {
    var s = Rt();
    n = n === void 0 ? null : n;
    var a = void 0;
    if (ze !== null) {
      var o = ze.memoizedState;
      if (((a = o.destroy), n !== null && wc(n, o.deps))) {
        s.memoizedState = Na(t, r, a, n);
        return;
      }
    }
    ((ke.flags |= e), (s.memoizedState = Na(1 | t, r, a, n)));
  }
  function Pm(e, t) {
    return Ko(8390656, 8, e, t);
  }
  function jc(e, t) {
    return Wo(2048, 8, e, t);
  }
  function Om(e, t) {
    return Wo(4, 2, e, t);
  }
  function Im(e, t) {
    return Wo(4, 4, e, t);
  }
  function Lm(e, t) {
    if (typeof t == "function")
      return (
        (e = e()),
        t(e),
        function () {
          t(null);
        }
      );
    if (t != null)
      return (
        (e = e()),
        (t.current = e),
        function () {
          t.current = null;
        }
      );
  }
  function Dm(e, t, r) {
    return (
      (r = r != null ? r.concat([e]) : null),
      Wo(4, 4, Lm.bind(null, t, e), r)
    );
  }
  function Nc() {}
  function zm(e, t) {
    var r = Rt();
    t = t === void 0 ? null : t;
    var n = r.memoizedState;
    return n !== null && t !== null && wc(t, n[1])
      ? n[0]
      : ((r.memoizedState = [e, t]), e);
  }
  function Mm(e, t) {
    var r = Rt();
    t = t === void 0 ? null : t;
    var n = r.memoizedState;
    return n !== null && t !== null && wc(t, n[1])
      ? n[0]
      : ((e = e()), (r.memoizedState = [e, t]), e);
  }
  function $m(e, t, r) {
    return (vn & 21) === 0
      ? (e.baseState && ((e.baseState = !1), (ut = !0)), (e.memoizedState = r))
      : (zt(r, t) ||
          ((r = hp()), (ke.lanes |= r), (xn |= r), (e.baseState = !0)),
        t);
  }
  function vv(e, t) {
    var r = ce;
    ((ce = r !== 0 && 4 > r ? r : 4), e(!0));
    var n = bc.transition;
    bc.transition = {};
    try {
      (e(!1), t());
    } finally {
      ((ce = r), (bc.transition = n));
    }
  }
  function Fm() {
    return Rt().memoizedState;
  }
  function xv(e, t, r) {
    var n = Zr(e);
    if (
      ((r = {
        lane: n,
        action: r,
        hasEagerState: !1,
        eagerState: null,
        next: null,
      }),
      Um(e))
    )
      Zm(t, r);
    else if (((r = vm(e, t, r, n)), r !== null)) {
      var s = nt();
      (Zt(r, e, n, s), Bm(r, t, n));
    }
  }
  function bv(e, t, r) {
    var n = Zr(e),
      s = {
        lane: n,
        action: r,
        hasEagerState: !1,
        eagerState: null,
        next: null,
      };
    if (Um(e)) Zm(t, s);
    else {
      var a = e.alternate;
      if (
        e.lanes === 0 &&
        (a === null || a.lanes === 0) &&
        ((a = t.lastRenderedReducer), a !== null)
      )
        try {
          var o = t.lastRenderedState,
            l = a(o, r);
          if (((s.hasEagerState = !0), (s.eagerState = l), zt(l, o))) {
            var c = t.interleaved;
            (c === null
              ? ((s.next = s), fc(t))
              : ((s.next = c.next), (c.next = s)),
              (t.interleaved = s));
            return;
          }
        } catch {}
      ((r = vm(e, t, s, n)),
        r !== null && ((s = nt()), Zt(r, e, n, s), Bm(r, t, n)));
    }
  }
  function Um(e) {
    var t = e.alternate;
    return e === ke || (t !== null && t === ke);
  }
  function Zm(e, t) {
    Sa = Vo = !0;
    var r = e.pending;
    (r === null ? (t.next = t) : ((t.next = r.next), (r.next = t)),
      (e.pending = t));
  }
  function Bm(e, t, r) {
    if ((r & 4194240) !== 0) {
      var n = t.lanes;
      ((n &= e.pendingLanes), (r |= n), (t.lanes = r), Al(e, r));
    }
  }
  var Ho = {
      readContext: Nt,
      useCallback: Ge,
      useContext: Ge,
      useEffect: Ge,
      useImperativeHandle: Ge,
      useInsertionEffect: Ge,
      useLayoutEffect: Ge,
      useMemo: Ge,
      useReducer: Ge,
      useRef: Ge,
      useState: Ge,
      useDebugValue: Ge,
      useDeferredValue: Ge,
      useTransition: Ge,
      useMutableSource: Ge,
      useSyncExternalStore: Ge,
      useId: Ge,
      unstable_isNewReconciler: !1,
    },
    wv = {
      readContext: Nt,
      useCallback: function (e, t) {
        return ((Gt().memoizedState = [e, t === void 0 ? null : t]), e);
      },
      useContext: Nt,
      useEffect: Pm,
      useImperativeHandle: function (e, t, r) {
        return (
          (r = r != null ? r.concat([e]) : null),
          Ko(4194308, 4, Lm.bind(null, t, e), r)
        );
      },
      useLayoutEffect: function (e, t) {
        return Ko(4194308, 4, e, t);
      },
      useInsertionEffect: function (e, t) {
        return Ko(4, 2, e, t);
      },
      useMemo: function (e, t) {
        var r = Gt();
        return (
          (t = t === void 0 ? null : t),
          (e = e()),
          (r.memoizedState = [e, t]),
          e
        );
      },
      useReducer: function (e, t, r) {
        var n = Gt();
        return (
          (t = r !== void 0 ? r(t) : t),
          (n.memoizedState = n.baseState = t),
          (e = {
            pending: null,
            interleaved: null,
            lanes: 0,
            dispatch: null,
            lastRenderedReducer: e,
            lastRenderedState: t,
          }),
          (n.queue = e),
          (e = e.dispatch = xv.bind(null, ke, e)),
          [n.memoizedState, e]
        );
      },
      useRef: function (e) {
        var t = Gt();
        return ((e = { current: e }), (t.memoizedState = e));
      },
      useState: Em,
      useDebugValue: Nc,
      useDeferredValue: function (e) {
        return (Gt().memoizedState = e);
      },
      useTransition: function () {
        var e = Em(!1),
          t = e[0];
        return ((e = vv.bind(null, e[1])), (Gt().memoizedState = e), [t, e]);
      },
      useMutableSource: function () {},
      useSyncExternalStore: function (e, t, r) {
        var n = ke,
          s = Gt();
        if (ye) {
          if (r === void 0) throw Error(E(407));
          r = r();
        } else {
          if (((r = t()), Ze === null)) throw Error(E(349));
          (vn & 30) !== 0 || Cm(n, t, r);
        }
        s.memoizedState = r;
        var a = { value: r, getSnapshot: t };
        return (
          (s.queue = a),
          Pm(Nm.bind(null, n, a, e), [e]),
          (n.flags |= 2048),
          Na(9, jm.bind(null, n, a, r, t), void 0, null),
          r
        );
      },
      useId: function () {
        var e = Gt(),
          t = Ze.identifierPrefix;
        if (ye) {
          var r = cr,
            n = lr;
          ((r = (n & ~(1 << (32 - Dt(n) - 1))).toString(32) + r),
            (t = ":" + t + "R" + r),
            (r = Ca++),
            0 < r && (t += "H" + r.toString(32)),
            (t += ":"));
        } else ((r = yv++), (t = ":" + t + "r" + r.toString(32) + ":"));
        return (e.memoizedState = t);
      },
      unstable_isNewReconciler: !1,
    },
    kv = {
      readContext: Nt,
      useCallback: zm,
      useContext: Nt,
      useEffect: jc,
      useImperativeHandle: Dm,
      useInsertionEffect: Om,
      useLayoutEffect: Im,
      useMemo: Mm,
      useReducer: Sc,
      useRef: Tm,
      useState: function () {
        return Sc(ja);
      },
      useDebugValue: Nc,
      useDeferredValue: function (e) {
        var t = Rt();
        return $m(t, ze.memoizedState, e);
      },
      useTransition: function () {
        var e = Sc(ja)[0],
          t = Rt().memoizedState;
        return [e, t];
      },
      useMutableSource: _m,
      useSyncExternalStore: Sm,
      useId: Fm,
      unstable_isNewReconciler: !1,
    },
    _v = {
      readContext: Nt,
      useCallback: zm,
      useContext: Nt,
      useEffect: jc,
      useImperativeHandle: Dm,
      useInsertionEffect: Om,
      useLayoutEffect: Im,
      useMemo: Mm,
      useReducer: Cc,
      useRef: Tm,
      useState: function () {
        return Cc(ja);
      },
      useDebugValue: Nc,
      useDeferredValue: function (e) {
        var t = Rt();
        return ze === null ? (t.memoizedState = e) : $m(t, ze.memoizedState, e);
      },
      useTransition: function () {
        var e = Cc(ja)[0],
          t = Rt().memoizedState;
        return [e, t];
      },
      useMutableSource: _m,
      useSyncExternalStore: Sm,
      useId: Fm,
      unstable_isNewReconciler: !1,
    };
  function $t(e, t) {
    if (e && e.defaultProps) {
      ((t = be({}, t)), (e = e.defaultProps));
      for (var r in e) t[r] === void 0 && (t[r] = e[r]);
      return t;
    }
    return t;
  }
  function Rc(e, t, r, n) {
    ((t = e.memoizedState),
      (r = r(n, t)),
      (r = r == null ? t : be({}, t, r)),
      (e.memoizedState = r),
      e.lanes === 0 && (e.updateQueue.baseState = r));
  }
  var Qo = {
    isMounted: function (e) {
      return (e = e._reactInternals) ? dn(e) === e : !1;
    },
    enqueueSetState: function (e, t, r) {
      e = e._reactInternals;
      var n = nt(),
        s = Zr(e),
        a = dr(n, s);
      ((a.payload = t),
        r != null && (a.callback = r),
        (t = Mr(e, a, s)),
        t !== null && (Zt(t, e, s, n), Uo(t, e, s)));
    },
    enqueueReplaceState: function (e, t, r) {
      e = e._reactInternals;
      var n = nt(),
        s = Zr(e),
        a = dr(n, s);
      ((a.tag = 1),
        (a.payload = t),
        r != null && (a.callback = r),
        (t = Mr(e, a, s)),
        t !== null && (Zt(t, e, s, n), Uo(t, e, s)));
    },
    enqueueForceUpdate: function (e, t) {
      e = e._reactInternals;
      var r = nt(),
        n = Zr(e),
        s = dr(r, n);
      ((s.tag = 2),
        t != null && (s.callback = t),
        (t = Mr(e, s, n)),
        t !== null && (Zt(t, e, n, r), Uo(t, e, n)));
    },
  };
  function qm(e, t, r, n, s, a, o) {
    return (
      (e = e.stateNode),
      typeof e.shouldComponentUpdate == "function"
        ? e.shouldComponentUpdate(n, a, o)
        : t.prototype && t.prototype.isPureReactComponent
          ? !ma(r, n) || !ma(s, a)
          : !0
    );
  }
  function Vm(e, t, r) {
    var n = !1,
      s = Lr,
      a = t.contextType;
    return (
      typeof a == "object" && a !== null
        ? (a = Nt(a))
        : ((s = ct(t) ? mn : Qe.current),
          (n = t.contextTypes),
          (a = (n = n != null) ? is(e, s) : Lr)),
      (t = new t(r, a)),
      (e.memoizedState =
        t.state !== null && t.state !== void 0 ? t.state : null),
      (t.updater = Qo),
      (e.stateNode = t),
      (t._reactInternals = e),
      n &&
        ((e = e.stateNode),
        (e.__reactInternalMemoizedUnmaskedChildContext = s),
        (e.__reactInternalMemoizedMaskedChildContext = a)),
      t
    );
  }
  function Km(e, t, r, n) {
    ((e = t.state),
      typeof t.componentWillReceiveProps == "function" &&
        t.componentWillReceiveProps(r, n),
      typeof t.UNSAFE_componentWillReceiveProps == "function" &&
        t.UNSAFE_componentWillReceiveProps(r, n),
      t.state !== e && Qo.enqueueReplaceState(t, t.state, null));
  }
  function Ac(e, t, r, n) {
    var s = e.stateNode;
    ((s.props = r), (s.state = e.memoizedState), (s.refs = {}), hc(e));
    var a = t.contextType;
    (typeof a == "object" && a !== null
      ? (s.context = Nt(a))
      : ((a = ct(t) ? mn : Qe.current), (s.context = is(e, a))),
      (s.state = e.memoizedState),
      (a = t.getDerivedStateFromProps),
      typeof a == "function" && (Rc(e, t, a, r), (s.state = e.memoizedState)),
      typeof t.getDerivedStateFromProps == "function" ||
        typeof s.getSnapshotBeforeUpdate == "function" ||
        (typeof s.UNSAFE_componentWillMount != "function" &&
          typeof s.componentWillMount != "function") ||
        ((t = s.state),
        typeof s.componentWillMount == "function" && s.componentWillMount(),
        typeof s.UNSAFE_componentWillMount == "function" &&
          s.UNSAFE_componentWillMount(),
        t !== s.state && Qo.enqueueReplaceState(s, s.state, null),
        Zo(e, r, s, n),
        (s.state = e.memoizedState)),
      typeof s.componentDidMount == "function" && (e.flags |= 4194308));
  }
  function hs(e, t) {
    try {
      var r = "",
        n = t;
      do ((r += Yy(n)), (n = n.return));
      while (n);
      var s = r;
    } catch (a) {
      s =
        `
Error generating stack: ` +
        a.message +
        `
` +
        a.stack;
    }
    return { value: e, source: t, stack: s, digest: null };
  }
  function Ec(e, t, r) {
    return { value: e, source: null, stack: r ?? null, digest: t ?? null };
  }
  function Tc(e, t) {
    try {
      console.error(t.value);
    } catch (r) {
      setTimeout(function () {
        throw r;
      });
    }
  }
  var Sv = typeof WeakMap == "function" ? WeakMap : Map;
  function Wm(e, t, r) {
    ((r = dr(-1, r)), (r.tag = 3), (r.payload = { element: null }));
    var n = t.value;
    return (
      (r.callback = function () {
        (ri || ((ri = !0), (Kc = n)), Tc(e, t));
      }),
      r
    );
  }
  function Hm(e, t, r) {
    ((r = dr(-1, r)), (r.tag = 3));
    var n = e.type.getDerivedStateFromError;
    if (typeof n == "function") {
      var s = t.value;
      ((r.payload = function () {
        return n(s);
      }),
        (r.callback = function () {
          Tc(e, t);
        }));
    }
    var a = e.stateNode;
    return (
      a !== null &&
        typeof a.componentDidCatch == "function" &&
        (r.callback = function () {
          (Tc(e, t),
            typeof n != "function" &&
              (Fr === null ? (Fr = new Set([this])) : Fr.add(this)));
          var o = t.stack;
          this.componentDidCatch(t.value, {
            componentStack: o !== null ? o : "",
          });
        }),
      r
    );
  }
  function Qm(e, t, r) {
    var n = e.pingCache;
    if (n === null) {
      n = e.pingCache = new Sv();
      var s = new Set();
      n.set(t, s);
    } else ((s = n.get(t)), s === void 0 && ((s = new Set()), n.set(t, s)));
    s.has(r) || (s.add(r), (e = Mv.bind(null, e, t, r)), t.then(e, e));
  }
  function Gm(e) {
    do {
      var t;
      if (
        ((t = e.tag === 13) &&
          ((t = e.memoizedState),
          (t = t !== null ? t.dehydrated !== null : !0)),
        t)
      )
        return e;
      e = e.return;
    } while (e !== null);
    return null;
  }
  function Ym(e, t, r, n, s) {
    return (e.mode & 1) === 0
      ? (e === t
          ? (e.flags |= 65536)
          : ((e.flags |= 128),
            (r.flags |= 131072),
            (r.flags &= -52805),
            r.tag === 1 &&
              (r.alternate === null
                ? (r.tag = 17)
                : ((t = dr(-1, 1)), (t.tag = 2), Mr(r, t, 1))),
            (r.lanes |= 1)),
        e)
      : ((e.flags |= 65536), (e.lanes = s), e);
  }
  var Cv = ar.ReactCurrentOwner,
    ut = !1;
  function rt(e, t, r, n) {
    t.child = e === null ? ym(t, null, r, n) : ds(t, e.child, r, n);
  }
  function Jm(e, t, r, n, s) {
    r = r.render;
    var a = t.ref;
    return (
      ms(t, s),
      (n = kc(e, t, r, n, a, s)),
      (r = _c()),
      e !== null && !ut
        ? ((t.updateQueue = e.updateQueue),
          (t.flags &= -2053),
          (e.lanes &= ~s),
          pr(e, t, s))
        : (ye && r && ac(t), (t.flags |= 1), rt(e, t, n, s), t.child)
    );
  }
  function Xm(e, t, r, n, s) {
    if (e === null) {
      var a = r.type;
      return typeof a == "function" &&
        !Xc(a) &&
        a.defaultProps === void 0 &&
        r.compare === null &&
        r.defaultProps === void 0
        ? ((t.tag = 15), (t.type = a), ef(e, t, a, n, s))
        : ((e = li(r.type, null, n, t, t.mode, s)),
          (e.ref = t.ref),
          (e.return = t),
          (t.child = e));
    }
    if (((a = e.child), (e.lanes & s) === 0)) {
      var o = a.memoizedProps;
      if (
        ((r = r.compare), (r = r !== null ? r : ma), r(o, n) && e.ref === t.ref)
      )
        return pr(e, t, s);
    }
    return (
      (t.flags |= 1),
      (e = qr(a, n)),
      (e.ref = t.ref),
      (e.return = t),
      (t.child = e)
    );
  }
  function ef(e, t, r, n, s) {
    if (e !== null) {
      var a = e.memoizedProps;
      if (ma(a, n) && e.ref === t.ref)
        if (((ut = !1), (t.pendingProps = n = a), (e.lanes & s) !== 0))
          (e.flags & 131072) !== 0 && (ut = !0);
        else return ((t.lanes = e.lanes), pr(e, t, s));
    }
    return Pc(e, t, r, n, s);
  }
  function tf(e, t, r) {
    var n = t.pendingProps,
      s = n.children,
      a = e !== null ? e.memoizedState : null;
    if (n.mode === "hidden")
      if ((t.mode & 1) === 0)
        ((t.memoizedState = {
          baseLanes: 0,
          cachePool: null,
          transitions: null,
        }),
          me(ys, kt),
          (kt |= r));
      else {
        if ((r & 1073741824) === 0)
          return (
            (e = a !== null ? a.baseLanes | r : r),
            (t.lanes = t.childLanes = 1073741824),
            (t.memoizedState = {
              baseLanes: e,
              cachePool: null,
              transitions: null,
            }),
            (t.updateQueue = null),
            me(ys, kt),
            (kt |= e),
            null
          );
        ((t.memoizedState = {
          baseLanes: 0,
          cachePool: null,
          transitions: null,
        }),
          (n = a !== null ? a.baseLanes : r),
          me(ys, kt),
          (kt |= n));
      }
    else
      (a !== null ? ((n = a.baseLanes | r), (t.memoizedState = null)) : (n = r),
        me(ys, kt),
        (kt |= n));
    return (rt(e, t, s, r), t.child);
  }
  function rf(e, t) {
    var r = t.ref;
    ((e === null && r !== null) || (e !== null && e.ref !== r)) &&
      ((t.flags |= 512), (t.flags |= 2097152));
  }
  function Pc(e, t, r, n, s) {
    var a = ct(r) ? mn : Qe.current;
    return (
      (a = is(t, a)),
      ms(t, s),
      (r = kc(e, t, r, n, a, s)),
      (n = _c()),
      e !== null && !ut
        ? ((t.updateQueue = e.updateQueue),
          (t.flags &= -2053),
          (e.lanes &= ~s),
          pr(e, t, s))
        : (ye && n && ac(t), (t.flags |= 1), rt(e, t, r, s), t.child)
    );
  }
  function nf(e, t, r, n, s) {
    if (ct(r)) {
      var a = !0;
      Oo(t);
    } else a = !1;
    if ((ms(t, s), t.stateNode === null))
      (Yo(e, t), Vm(t, r, n), Ac(t, r, n, s), (n = !0));
    else if (e === null) {
      var o = t.stateNode,
        l = t.memoizedProps;
      o.props = l;
      var c = o.context,
        u = r.contextType;
      typeof u == "object" && u !== null
        ? (u = Nt(u))
        : ((u = ct(r) ? mn : Qe.current), (u = is(t, u)));
      var d = r.getDerivedStateFromProps,
        f =
          typeof d == "function" ||
          typeof o.getSnapshotBeforeUpdate == "function";
      (f ||
        (typeof o.UNSAFE_componentWillReceiveProps != "function" &&
          typeof o.componentWillReceiveProps != "function") ||
        ((l !== n || c !== u) && Km(t, o, n, u)),
        (zr = !1));
      var m = t.memoizedState;
      ((o.state = m),
        Zo(t, n, o, s),
        (c = t.memoizedState),
        l !== n || m !== c || lt.current || zr
          ? (typeof d == "function" && (Rc(t, r, d, n), (c = t.memoizedState)),
            (l = zr || qm(t, r, l, n, m, c, u))
              ? (f ||
                  (typeof o.UNSAFE_componentWillMount != "function" &&
                    typeof o.componentWillMount != "function") ||
                  (typeof o.componentWillMount == "function" &&
                    o.componentWillMount(),
                  typeof o.UNSAFE_componentWillMount == "function" &&
                    o.UNSAFE_componentWillMount()),
                typeof o.componentDidMount == "function" &&
                  (t.flags |= 4194308))
              : (typeof o.componentDidMount == "function" &&
                  (t.flags |= 4194308),
                (t.memoizedProps = n),
                (t.memoizedState = c)),
            (o.props = n),
            (o.state = c),
            (o.context = u),
            (n = l))
          : (typeof o.componentDidMount == "function" && (t.flags |= 4194308),
            (n = !1)));
    } else {
      ((o = t.stateNode),
        xm(e, t),
        (l = t.memoizedProps),
        (u = t.type === t.elementType ? l : $t(t.type, l)),
        (o.props = u),
        (f = t.pendingProps),
        (m = o.context),
        (c = r.contextType),
        typeof c == "object" && c !== null
          ? (c = Nt(c))
          : ((c = ct(r) ? mn : Qe.current), (c = is(t, c))));
      var v = r.getDerivedStateFromProps;
      ((d =
        typeof v == "function" ||
        typeof o.getSnapshotBeforeUpdate == "function") ||
        (typeof o.UNSAFE_componentWillReceiveProps != "function" &&
          typeof o.componentWillReceiveProps != "function") ||
        ((l !== f || m !== c) && Km(t, o, n, c)),
        (zr = !1),
        (m = t.memoizedState),
        (o.state = m),
        Zo(t, n, o, s));
      var x = t.memoizedState;
      l !== f || m !== x || lt.current || zr
        ? (typeof v == "function" && (Rc(t, r, v, n), (x = t.memoizedState)),
          (u = zr || qm(t, r, u, n, m, x, c) || !1)
            ? (d ||
                (typeof o.UNSAFE_componentWillUpdate != "function" &&
                  typeof o.componentWillUpdate != "function") ||
                (typeof o.componentWillUpdate == "function" &&
                  o.componentWillUpdate(n, x, c),
                typeof o.UNSAFE_componentWillUpdate == "function" &&
                  o.UNSAFE_componentWillUpdate(n, x, c)),
              typeof o.componentDidUpdate == "function" && (t.flags |= 4),
              typeof o.getSnapshotBeforeUpdate == "function" &&
                (t.flags |= 1024))
            : (typeof o.componentDidUpdate != "function" ||
                (l === e.memoizedProps && m === e.memoizedState) ||
                (t.flags |= 4),
              typeof o.getSnapshotBeforeUpdate != "function" ||
                (l === e.memoizedProps && m === e.memoizedState) ||
                (t.flags |= 1024),
              (t.memoizedProps = n),
              (t.memoizedState = x)),
          (o.props = n),
          (o.state = x),
          (o.context = c),
          (n = u))
        : (typeof o.componentDidUpdate != "function" ||
            (l === e.memoizedProps && m === e.memoizedState) ||
            (t.flags |= 4),
          typeof o.getSnapshotBeforeUpdate != "function" ||
            (l === e.memoizedProps && m === e.memoizedState) ||
            (t.flags |= 1024),
          (n = !1));
    }
    return Oc(e, t, r, n, a, s);
  }
  function Oc(e, t, r, n, s, a) {
    rf(e, t);
    var o = (t.flags & 128) !== 0;
    if (!n && !o) return (s && lm(t, r, !1), pr(e, t, a));
    ((n = t.stateNode), (Cv.current = t));
    var l =
      o && typeof r.getDerivedStateFromError != "function" ? null : n.render();
    return (
      (t.flags |= 1),
      e !== null && o
        ? ((t.child = ds(t, e.child, null, a)), (t.child = ds(t, null, l, a)))
        : rt(e, t, l, a),
      (t.memoizedState = n.state),
      s && lm(t, r, !0),
      t.child
    );
  }
  function sf(e) {
    var t = e.stateNode;
    (t.pendingContext
      ? om(e, t.pendingContext, t.pendingContext !== t.context)
      : t.context && om(e, t.context, !1),
      gc(e, t.containerInfo));
  }
  function af(e, t, r, n, s) {
    return (us(), cc(s), (t.flags |= 256), rt(e, t, r, n), t.child);
  }
  var Ic = { dehydrated: null, treeContext: null, retryLane: 0 };
  function Lc(e) {
    return { baseLanes: e, cachePool: null, transitions: null };
  }
  function of(e, t, r) {
    var n = t.pendingProps,
      s = we.current,
      a = !1,
      o = (t.flags & 128) !== 0,
      l;
    if (
      ((l = o) ||
        (l = e !== null && e.memoizedState === null ? !1 : (s & 2) !== 0),
      l
        ? ((a = !0), (t.flags &= -129))
        : (e === null || e.memoizedState !== null) && (s |= 1),
      me(we, s & 1),
      e === null)
    )
      return (
        lc(t),
        (e = t.memoizedState),
        e !== null && ((e = e.dehydrated), e !== null)
          ? ((t.mode & 1) === 0
              ? (t.lanes = 1)
              : e.data === "$!"
                ? (t.lanes = 8)
                : (t.lanes = 1073741824),
            null)
          : ((o = n.children),
            (e = n.fallback),
            a
              ? ((n = t.mode),
                (a = t.child),
                (o = { mode: "hidden", children: o }),
                (n & 1) === 0 && a !== null
                  ? ((a.childLanes = 0), (a.pendingProps = o))
                  : (a = ci(o, n, 0, null)),
                (e = _n(e, n, r, null)),
                (a.return = t),
                (e.return = t),
                (a.sibling = e),
                (t.child = a),
                (t.child.memoizedState = Lc(r)),
                (t.memoizedState = Ic),
                e)
              : Dc(t, o))
      );
    if (((s = e.memoizedState), s !== null && ((l = s.dehydrated), l !== null)))
      return jv(e, t, o, n, l, s, r);
    if (a) {
      ((a = n.fallback), (o = t.mode), (s = e.child), (l = s.sibling));
      var c = { mode: "hidden", children: n.children };
      return (
        (o & 1) === 0 && t.child !== s
          ? ((n = t.child),
            (n.childLanes = 0),
            (n.pendingProps = c),
            (t.deletions = null))
          : ((n = qr(s, c)), (n.subtreeFlags = s.subtreeFlags & 14680064)),
        l !== null ? (a = qr(l, a)) : ((a = _n(a, o, r, null)), (a.flags |= 2)),
        (a.return = t),
        (n.return = t),
        (n.sibling = a),
        (t.child = n),
        (n = a),
        (a = t.child),
        (o = e.child.memoizedState),
        (o =
          o === null
            ? Lc(r)
            : {
                baseLanes: o.baseLanes | r,
                cachePool: null,
                transitions: o.transitions,
              }),
        (a.memoizedState = o),
        (a.childLanes = e.childLanes & ~r),
        (t.memoizedState = Ic),
        n
      );
    }
    return (
      (a = e.child),
      (e = a.sibling),
      (n = qr(a, { mode: "visible", children: n.children })),
      (t.mode & 1) === 0 && (n.lanes = r),
      (n.return = t),
      (n.sibling = null),
      e !== null &&
        ((r = t.deletions),
        r === null ? ((t.deletions = [e]), (t.flags |= 16)) : r.push(e)),
      (t.child = n),
      (t.memoizedState = null),
      n
    );
  }
  function Dc(e, t) {
    return (
      (t = ci({ mode: "visible", children: t }, e.mode, 0, null)),
      (t.return = e),
      (e.child = t)
    );
  }
  function Go(e, t, r, n) {
    return (
      n !== null && cc(n),
      ds(t, e.child, null, r),
      (e = Dc(t, t.pendingProps.children)),
      (e.flags |= 2),
      (t.memoizedState = null),
      e
    );
  }
  function jv(e, t, r, n, s, a, o) {
    if (r)
      return t.flags & 256
        ? ((t.flags &= -257), (n = Ec(Error(E(422)))), Go(e, t, o, n))
        : t.memoizedState !== null
          ? ((t.child = e.child), (t.flags |= 128), null)
          : ((a = n.fallback),
            (s = t.mode),
            (n = ci({ mode: "visible", children: n.children }, s, 0, null)),
            (a = _n(a, s, o, null)),
            (a.flags |= 2),
            (n.return = t),
            (a.return = t),
            (n.sibling = a),
            (t.child = n),
            (t.mode & 1) !== 0 && ds(t, e.child, null, o),
            (t.child.memoizedState = Lc(o)),
            (t.memoizedState = Ic),
            a);
    if ((t.mode & 1) === 0) return Go(e, t, o, null);
    if (s.data === "$!") {
      if (((n = s.nextSibling && s.nextSibling.dataset), n)) var l = n.dgst;
      return (
        (n = l),
        (a = Error(E(419))),
        (n = Ec(a, n, void 0)),
        Go(e, t, o, n)
      );
    }
    if (((l = (o & e.childLanes) !== 0), ut || l)) {
      if (((n = Ze), n !== null)) {
        switch (o & -o) {
          case 4:
            s = 2;
            break;
          case 16:
            s = 8;
            break;
          case 64:
          case 128:
          case 256:
          case 512:
          case 1024:
          case 2048:
          case 4096:
          case 8192:
          case 16384:
          case 32768:
          case 65536:
          case 131072:
          case 262144:
          case 524288:
          case 1048576:
          case 2097152:
          case 4194304:
          case 8388608:
          case 16777216:
          case 33554432:
          case 67108864:
            s = 32;
            break;
          case 536870912:
            s = 268435456;
            break;
          default:
            s = 0;
        }
        ((s = (s & (n.suspendedLanes | o)) !== 0 ? 0 : s),
          s !== 0 &&
            s !== a.retryLane &&
            ((a.retryLane = s), ur(e, s), Zt(n, e, s, -1)));
      }
      return (Jc(), (n = Ec(Error(E(421)))), Go(e, t, o, n));
    }
    return s.data === "$?"
      ? ((t.flags |= 128),
        (t.child = e.child),
        (t = $v.bind(null, e)),
        (s._reactRetry = t),
        null)
      : ((e = a.treeContext),
        (wt = Or(s.nextSibling)),
        (bt = t),
        (ye = !0),
        (Mt = null),
        e !== null &&
          ((Ct[jt++] = lr),
          (Ct[jt++] = cr),
          (Ct[jt++] = fn),
          (lr = e.id),
          (cr = e.overflow),
          (fn = t)),
        (t = Dc(t, n.children)),
        (t.flags |= 4096),
        t);
  }
  function lf(e, t, r) {
    e.lanes |= t;
    var n = e.alternate;
    (n !== null && (n.lanes |= t), mc(e.return, t, r));
  }
  function zc(e, t, r, n, s) {
    var a = e.memoizedState;
    a === null
      ? (e.memoizedState = {
          isBackwards: t,
          rendering: null,
          renderingStartTime: 0,
          last: n,
          tail: r,
          tailMode: s,
        })
      : ((a.isBackwards = t),
        (a.rendering = null),
        (a.renderingStartTime = 0),
        (a.last = n),
        (a.tail = r),
        (a.tailMode = s));
  }
  function cf(e, t, r) {
    var n = t.pendingProps,
      s = n.revealOrder,
      a = n.tail;
    if ((rt(e, t, n.children, r), (n = we.current), (n & 2) !== 0))
      ((n = (n & 1) | 2), (t.flags |= 128));
    else {
      if (e !== null && (e.flags & 128) !== 0)
        e: for (e = t.child; e !== null; ) {
          if (e.tag === 13) e.memoizedState !== null && lf(e, r, t);
          else if (e.tag === 19) lf(e, r, t);
          else if (e.child !== null) {
            ((e.child.return = e), (e = e.child));
            continue;
          }
          if (e === t) break e;
          for (; e.sibling === null; ) {
            if (e.return === null || e.return === t) break e;
            e = e.return;
          }
          ((e.sibling.return = e.return), (e = e.sibling));
        }
      n &= 1;
    }
    if ((me(we, n), (t.mode & 1) === 0)) t.memoizedState = null;
    else
      switch (s) {
        case "forwards":
          for (r = t.child, s = null; r !== null; )
            ((e = r.alternate),
              e !== null && Bo(e) === null && (s = r),
              (r = r.sibling));
          ((r = s),
            r === null
              ? ((s = t.child), (t.child = null))
              : ((s = r.sibling), (r.sibling = null)),
            zc(t, !1, s, r, a));
          break;
        case "backwards":
          for (r = null, s = t.child, t.child = null; s !== null; ) {
            if (((e = s.alternate), e !== null && Bo(e) === null)) {
              t.child = s;
              break;
            }
            ((e = s.sibling), (s.sibling = r), (r = s), (s = e));
          }
          zc(t, !0, r, null, a);
          break;
        case "together":
          zc(t, !1, null, null, void 0);
          break;
        default:
          t.memoizedState = null;
      }
    return t.child;
  }
  function Yo(e, t) {
    (t.mode & 1) === 0 &&
      e !== null &&
      ((e.alternate = null), (t.alternate = null), (t.flags |= 2));
  }
  function pr(e, t, r) {
    if (
      (e !== null && (t.dependencies = e.dependencies),
      (xn |= t.lanes),
      (r & t.childLanes) === 0)
    )
      return null;
    if (e !== null && t.child !== e.child) throw Error(E(153));
    if (t.child !== null) {
      for (
        e = t.child, r = qr(e, e.pendingProps), t.child = r, r.return = t;
        e.sibling !== null;
      )
        ((e = e.sibling),
          (r = r.sibling = qr(e, e.pendingProps)),
          (r.return = t));
      r.sibling = null;
    }
    return t.child;
  }
  function Nv(e, t, r) {
    switch (t.tag) {
      case 3:
        (sf(t), us());
        break;
      case 5:
        km(t);
        break;
      case 1:
        ct(t.type) && Oo(t);
        break;
      case 4:
        gc(t, t.stateNode.containerInfo);
        break;
      case 10:
        var n = t.type._context,
          s = t.memoizedProps.value;
        (me($o, n._currentValue), (n._currentValue = s));
        break;
      case 13:
        if (((n = t.memoizedState), n !== null))
          return n.dehydrated !== null
            ? (me(we, we.current & 1), (t.flags |= 128), null)
            : (r & t.child.childLanes) !== 0
              ? of(e, t, r)
              : (me(we, we.current & 1),
                (e = pr(e, t, r)),
                e !== null ? e.sibling : null);
        me(we, we.current & 1);
        break;
      case 19:
        if (((n = (r & t.childLanes) !== 0), (e.flags & 128) !== 0)) {
          if (n) return cf(e, t, r);
          t.flags |= 128;
        }
        if (
          ((s = t.memoizedState),
          s !== null &&
            ((s.rendering = null), (s.tail = null), (s.lastEffect = null)),
          me(we, we.current),
          n)
        )
          break;
        return null;
      case 22:
      case 23:
        return ((t.lanes = 0), tf(e, t, r));
    }
    return pr(e, t, r);
  }
  var uf, Mc, df, pf;
  ((uf = function (e, t) {
    for (var r = t.child; r !== null; ) {
      if (r.tag === 5 || r.tag === 6) e.appendChild(r.stateNode);
      else if (r.tag !== 4 && r.child !== null) {
        ((r.child.return = r), (r = r.child));
        continue;
      }
      if (r === t) break;
      for (; r.sibling === null; ) {
        if (r.return === null || r.return === t) return;
        r = r.return;
      }
      ((r.sibling.return = r.return), (r = r.sibling));
    }
  }),
    (Mc = function () {}),
    (df = function (e, t, r, n) {
      var s = e.memoizedProps;
      if (s !== n) {
        ((e = t.stateNode), yn(Qt.current));
        var a = null;
        switch (r) {
          case "input":
            ((s = ml(e, s)), (n = ml(e, n)), (a = []));
            break;
          case "select":
            ((s = be({}, s, { value: void 0 })),
              (n = be({}, n, { value: void 0 })),
              (a = []));
            break;
          case "textarea":
            ((s = gl(e, s)), (n = gl(e, n)), (a = []));
            break;
          default:
            typeof s.onClick != "function" &&
              typeof n.onClick == "function" &&
              (e.onclick = Eo);
        }
        vl(r, n);
        var o;
        r = null;
        for (u in s)
          if (!n.hasOwnProperty(u) && s.hasOwnProperty(u) && s[u] != null)
            if (u === "style") {
              var l = s[u];
              for (o in l) l.hasOwnProperty(o) && (r || (r = {}), (r[o] = ""));
            } else
              u !== "dangerouslySetInnerHTML" &&
                u !== "children" &&
                u !== "suppressContentEditableWarning" &&
                u !== "suppressHydrationWarning" &&
                u !== "autoFocus" &&
                (Ks.hasOwnProperty(u)
                  ? a || (a = [])
                  : (a = a || []).push(u, null));
        for (u in n) {
          var c = n[u];
          if (
            ((l = s?.[u]),
            n.hasOwnProperty(u) && c !== l && (c != null || l != null))
          )
            if (u === "style")
              if (l) {
                for (o in l)
                  !l.hasOwnProperty(o) ||
                    (c && c.hasOwnProperty(o)) ||
                    (r || (r = {}), (r[o] = ""));
                for (o in c)
                  c.hasOwnProperty(o) &&
                    l[o] !== c[o] &&
                    (r || (r = {}), (r[o] = c[o]));
              } else (r || (a || (a = []), a.push(u, r)), (r = c));
            else
              u === "dangerouslySetInnerHTML"
                ? ((c = c ? c.__html : void 0),
                  (l = l ? l.__html : void 0),
                  c != null && l !== c && (a = a || []).push(u, c))
                : u === "children"
                  ? (typeof c != "string" && typeof c != "number") ||
                    (a = a || []).push(u, "" + c)
                  : u !== "suppressContentEditableWarning" &&
                    u !== "suppressHydrationWarning" &&
                    (Ks.hasOwnProperty(u)
                      ? (c != null && u === "onScroll" && he("scroll", e),
                        a || l === c || (a = []))
                      : (a = a || []).push(u, c));
        }
        r && (a = a || []).push("style", r);
        var u = a;
        (t.updateQueue = u) && (t.flags |= 4);
      }
    }),
    (pf = function (e, t, r, n) {
      r !== n && (t.flags |= 4);
    }));
  function Ra(e, t) {
    if (!ye)
      switch (e.tailMode) {
        case "hidden":
          t = e.tail;
          for (var r = null; t !== null; )
            (t.alternate !== null && (r = t), (t = t.sibling));
          r === null ? (e.tail = null) : (r.sibling = null);
          break;
        case "collapsed":
          r = e.tail;
          for (var n = null; r !== null; )
            (r.alternate !== null && (n = r), (r = r.sibling));
          n === null
            ? t || e.tail === null
              ? (e.tail = null)
              : (e.tail.sibling = null)
            : (n.sibling = null);
      }
  }
  function Ye(e) {
    var t = e.alternate !== null && e.alternate.child === e.child,
      r = 0,
      n = 0;
    if (t)
      for (var s = e.child; s !== null; )
        ((r |= s.lanes | s.childLanes),
          (n |= s.subtreeFlags & 14680064),
          (n |= s.flags & 14680064),
          (s.return = e),
          (s = s.sibling));
    else
      for (s = e.child; s !== null; )
        ((r |= s.lanes | s.childLanes),
          (n |= s.subtreeFlags),
          (n |= s.flags),
          (s.return = e),
          (s = s.sibling));
    return ((e.subtreeFlags |= n), (e.childLanes = r), t);
  }
  function Rv(e, t, r) {
    var n = t.pendingProps;
    switch ((oc(t), t.tag)) {
      case 2:
      case 16:
      case 15:
      case 0:
      case 11:
      case 7:
      case 8:
      case 12:
      case 9:
      case 14:
        return (Ye(t), null);
      case 1:
        return (ct(t.type) && Po(), Ye(t), null);
      case 3:
        return (
          (n = t.stateNode),
          fs(),
          ge(lt),
          ge(Qe),
          xc(),
          n.pendingContext &&
            ((n.context = n.pendingContext), (n.pendingContext = null)),
          (e === null || e.child === null) &&
            (zo(t)
              ? (t.flags |= 4)
              : e === null ||
                (e.memoizedState.isDehydrated && (t.flags & 256) === 0) ||
                ((t.flags |= 1024), Mt !== null && (Qc(Mt), (Mt = null)))),
          Mc(e, t),
          Ye(t),
          null
        );
      case 5:
        yc(t);
        var s = yn(_a.current);
        if (((r = t.type), e !== null && t.stateNode != null))
          (df(e, t, r, n, s),
            e.ref !== t.ref && ((t.flags |= 512), (t.flags |= 2097152)));
        else {
          if (!n) {
            if (t.stateNode === null) throw Error(E(166));
            return (Ye(t), null);
          }
          if (((e = yn(Qt.current)), zo(t))) {
            ((n = t.stateNode), (r = t.type));
            var a = t.memoizedProps;
            switch (((n[Ht] = t), (n[va] = a), (e = (t.mode & 1) !== 0), r)) {
              case "dialog":
                (he("cancel", n), he("close", n));
                break;
              case "iframe":
              case "object":
              case "embed":
                he("load", n);
                break;
              case "video":
              case "audio":
                for (s = 0; s < ha.length; s++) he(ha[s], n);
                break;
              case "source":
                he("error", n);
                break;
              case "img":
              case "image":
              case "link":
                (he("error", n), he("load", n));
                break;
              case "details":
                he("toggle", n);
                break;
              case "input":
                (Vd(n, a), he("invalid", n));
                break;
              case "select":
                ((n._wrapperState = { wasMultiple: !!a.multiple }),
                  he("invalid", n));
                break;
              case "textarea":
                (Hd(n, a), he("invalid", n));
            }
            (vl(r, a), (s = null));
            for (var o in a)
              if (a.hasOwnProperty(o)) {
                var l = a[o];
                o === "children"
                  ? typeof l == "string"
                    ? n.textContent !== l &&
                      (a.suppressHydrationWarning !== !0 &&
                        Ao(n.textContent, l, e),
                      (s = ["children", l]))
                    : typeof l == "number" &&
                      n.textContent !== "" + l &&
                      (a.suppressHydrationWarning !== !0 &&
                        Ao(n.textContent, l, e),
                      (s = ["children", "" + l]))
                  : Ks.hasOwnProperty(o) &&
                    l != null &&
                    o === "onScroll" &&
                    he("scroll", n);
              }
            switch (r) {
              case "input":
                (ao(n), Wd(n, a, !0));
                break;
              case "textarea":
                (ao(n), Gd(n));
                break;
              case "select":
              case "option":
                break;
              default:
                typeof a.onClick == "function" && (n.onclick = Eo);
            }
            ((n = s), (t.updateQueue = n), n !== null && (t.flags |= 4));
          } else {
            ((o = s.nodeType === 9 ? s : s.ownerDocument),
              e === "http://www.w3.org/1999/xhtml" && (e = Yd(r)),
              e === "http://www.w3.org/1999/xhtml"
                ? r === "script"
                  ? ((e = o.createElement("div")),
                    (e.innerHTML = "<script><\/script>"),
                    (e = e.removeChild(e.firstChild)))
                  : typeof n.is == "string"
                    ? (e = o.createElement(r, { is: n.is }))
                    : ((e = o.createElement(r)),
                      r === "select" &&
                        ((o = e),
                        n.multiple
                          ? (o.multiple = !0)
                          : n.size && (o.size = n.size)))
                : (e = o.createElementNS(e, r)),
              (e[Ht] = t),
              (e[va] = n),
              uf(e, t, !1, !1),
              (t.stateNode = e));
            e: {
              switch (((o = xl(r, n)), r)) {
                case "dialog":
                  (he("cancel", e), he("close", e), (s = n));
                  break;
                case "iframe":
                case "object":
                case "embed":
                  (he("load", e), (s = n));
                  break;
                case "video":
                case "audio":
                  for (s = 0; s < ha.length; s++) he(ha[s], e);
                  s = n;
                  break;
                case "source":
                  (he("error", e), (s = n));
                  break;
                case "img":
                case "image":
                case "link":
                  (he("error", e), he("load", e), (s = n));
                  break;
                case "details":
                  (he("toggle", e), (s = n));
                  break;
                case "input":
                  (Vd(e, n), (s = ml(e, n)), he("invalid", e));
                  break;
                case "option":
                  s = n;
                  break;
                case "select":
                  ((e._wrapperState = { wasMultiple: !!n.multiple }),
                    (s = be({}, n, { value: void 0 })),
                    he("invalid", e));
                  break;
                case "textarea":
                  (Hd(e, n), (s = gl(e, n)), he("invalid", e));
                  break;
                default:
                  s = n;
              }
              (vl(r, s), (l = s));
              for (a in l)
                if (l.hasOwnProperty(a)) {
                  var c = l[a];
                  a === "style"
                    ? ep(e, c)
                    : a === "dangerouslySetInnerHTML"
                      ? ((c = c ? c.__html : void 0), c != null && Jd(e, c))
                      : a === "children"
                        ? typeof c == "string"
                          ? (r !== "textarea" || c !== "") && Gs(e, c)
                          : typeof c == "number" && Gs(e, "" + c)
                        : a !== "suppressContentEditableWarning" &&
                          a !== "suppressHydrationWarning" &&
                          a !== "autoFocus" &&
                          (Ks.hasOwnProperty(a)
                            ? c != null && a === "onScroll" && he("scroll", e)
                            : c != null && rl(e, a, c, o));
                }
              switch (r) {
                case "input":
                  (ao(e), Wd(e, n, !1));
                  break;
                case "textarea":
                  (ao(e), Gd(e));
                  break;
                case "option":
                  n.value != null && e.setAttribute("value", "" + jr(n.value));
                  break;
                case "select":
                  ((e.multiple = !!n.multiple),
                    (a = n.value),
                    a != null
                      ? Qn(e, !!n.multiple, a, !1)
                      : n.defaultValue != null &&
                        Qn(e, !!n.multiple, n.defaultValue, !0));
                  break;
                default:
                  typeof s.onClick == "function" && (e.onclick = Eo);
              }
              switch (r) {
                case "button":
                case "input":
                case "select":
                case "textarea":
                  n = !!n.autoFocus;
                  break e;
                case "img":
                  n = !0;
                  break e;
                default:
                  n = !1;
              }
            }
            n && (t.flags |= 4);
          }
          t.ref !== null && ((t.flags |= 512), (t.flags |= 2097152));
        }
        return (Ye(t), null);
      case 6:
        if (e && t.stateNode != null) pf(e, t, e.memoizedProps, n);
        else {
          if (typeof n != "string" && t.stateNode === null) throw Error(E(166));
          if (((r = yn(_a.current)), yn(Qt.current), zo(t))) {
            if (
              ((n = t.stateNode),
              (r = t.memoizedProps),
              (n[Ht] = t),
              (a = n.nodeValue !== r) && ((e = bt), e !== null))
            )
              switch (e.tag) {
                case 3:
                  Ao(n.nodeValue, r, (e.mode & 1) !== 0);
                  break;
                case 5:
                  e.memoizedProps.suppressHydrationWarning !== !0 &&
                    Ao(n.nodeValue, r, (e.mode & 1) !== 0);
              }
            a && (t.flags |= 4);
          } else
            ((n = (r.nodeType === 9 ? r : r.ownerDocument).createTextNode(n)),
              (n[Ht] = t),
              (t.stateNode = n));
        }
        return (Ye(t), null);
      case 13:
        if (
          (ge(we),
          (n = t.memoizedState),
          e === null ||
            (e.memoizedState !== null && e.memoizedState.dehydrated !== null))
        ) {
          if (ye && wt !== null && (t.mode & 1) !== 0 && (t.flags & 128) === 0)
            (fm(), us(), (t.flags |= 98560), (a = !1));
          else if (((a = zo(t)), n !== null && n.dehydrated !== null)) {
            if (e === null) {
              if (!a) throw Error(E(318));
              if (
                ((a = t.memoizedState),
                (a = a !== null ? a.dehydrated : null),
                !a)
              )
                throw Error(E(317));
              a[Ht] = t;
            } else
              (us(),
                (t.flags & 128) === 0 && (t.memoizedState = null),
                (t.flags |= 4));
            (Ye(t), (a = !1));
          } else (Mt !== null && (Qc(Mt), (Mt = null)), (a = !0));
          if (!a) return t.flags & 65536 ? t : null;
        }
        return (t.flags & 128) !== 0
          ? ((t.lanes = r), t)
          : ((n = n !== null),
            n !== (e !== null && e.memoizedState !== null) &&
              n &&
              ((t.child.flags |= 8192),
              (t.mode & 1) !== 0 &&
                (e === null || (we.current & 1) !== 0
                  ? Me === 0 && (Me = 3)
                  : Jc())),
            t.updateQueue !== null && (t.flags |= 4),
            Ye(t),
            null);
      case 4:
        return (
          fs(),
          Mc(e, t),
          e === null && ga(t.stateNode.containerInfo),
          Ye(t),
          null
        );
      case 10:
        return (pc(t.type._context), Ye(t), null);
      case 17:
        return (ct(t.type) && Po(), Ye(t), null);
      case 19:
        if ((ge(we), (a = t.memoizedState), a === null)) return (Ye(t), null);
        if (((n = (t.flags & 128) !== 0), (o = a.rendering), o === null))
          if (n) Ra(a, !1);
          else {
            if (Me !== 0 || (e !== null && (e.flags & 128) !== 0))
              for (e = t.child; e !== null; ) {
                if (((o = Bo(e)), o !== null)) {
                  for (
                    t.flags |= 128,
                      Ra(a, !1),
                      n = o.updateQueue,
                      n !== null && ((t.updateQueue = n), (t.flags |= 4)),
                      t.subtreeFlags = 0,
                      n = r,
                      r = t.child;
                    r !== null;
                  )
                    ((a = r),
                      (e = n),
                      (a.flags &= 14680066),
                      (o = a.alternate),
                      o === null
                        ? ((a.childLanes = 0),
                          (a.lanes = e),
                          (a.child = null),
                          (a.subtreeFlags = 0),
                          (a.memoizedProps = null),
                          (a.memoizedState = null),
                          (a.updateQueue = null),
                          (a.dependencies = null),
                          (a.stateNode = null))
                        : ((a.childLanes = o.childLanes),
                          (a.lanes = o.lanes),
                          (a.child = o.child),
                          (a.subtreeFlags = 0),
                          (a.deletions = null),
                          (a.memoizedProps = o.memoizedProps),
                          (a.memoizedState = o.memoizedState),
                          (a.updateQueue = o.updateQueue),
                          (a.type = o.type),
                          (e = o.dependencies),
                          (a.dependencies =
                            e === null
                              ? null
                              : {
                                  lanes: e.lanes,
                                  firstContext: e.firstContext,
                                })),
                      (r = r.sibling));
                  return (me(we, (we.current & 1) | 2), t.child);
                }
                e = e.sibling;
              }
            a.tail !== null &&
              Pe() > vs &&
              ((t.flags |= 128), (n = !0), Ra(a, !1), (t.lanes = 4194304));
          }
        else {
          if (!n)
            if (((e = Bo(o)), e !== null)) {
              if (
                ((t.flags |= 128),
                (n = !0),
                (r = e.updateQueue),
                r !== null && ((t.updateQueue = r), (t.flags |= 4)),
                Ra(a, !0),
                a.tail === null &&
                  a.tailMode === "hidden" &&
                  !o.alternate &&
                  !ye)
              )
                return (Ye(t), null);
            } else
              2 * Pe() - a.renderingStartTime > vs &&
                r !== 1073741824 &&
                ((t.flags |= 128), (n = !0), Ra(a, !1), (t.lanes = 4194304));
          a.isBackwards
            ? ((o.sibling = t.child), (t.child = o))
            : ((r = a.last),
              r !== null ? (r.sibling = o) : (t.child = o),
              (a.last = o));
        }
        return a.tail !== null
          ? ((t = a.tail),
            (a.rendering = t),
            (a.tail = t.sibling),
            (a.renderingStartTime = Pe()),
            (t.sibling = null),
            (r = we.current),
            me(we, n ? (r & 1) | 2 : r & 1),
            t)
          : (Ye(t), null);
      case 22:
      case 23:
        return (
          Yc(),
          (n = t.memoizedState !== null),
          e !== null && (e.memoizedState !== null) !== n && (t.flags |= 8192),
          n && (t.mode & 1) !== 0
            ? (kt & 1073741824) !== 0 &&
              (Ye(t), t.subtreeFlags & 6 && (t.flags |= 8192))
            : Ye(t),
          null
        );
      case 24:
        return null;
      case 25:
        return null;
    }
    throw Error(E(156, t.tag));
  }
  function Av(e, t) {
    switch ((oc(t), t.tag)) {
      case 1:
        return (
          ct(t.type) && Po(),
          (e = t.flags),
          e & 65536 ? ((t.flags = (e & -65537) | 128), t) : null
        );
      case 3:
        return (
          fs(),
          ge(lt),
          ge(Qe),
          xc(),
          (e = t.flags),
          (e & 65536) !== 0 && (e & 128) === 0
            ? ((t.flags = (e & -65537) | 128), t)
            : null
        );
      case 5:
        return (yc(t), null);
      case 13:
        if (
          (ge(we), (e = t.memoizedState), e !== null && e.dehydrated !== null)
        ) {
          if (t.alternate === null) throw Error(E(340));
          us();
        }
        return (
          (e = t.flags),
          e & 65536 ? ((t.flags = (e & -65537) | 128), t) : null
        );
      case 19:
        return (ge(we), null);
      case 4:
        return (fs(), null);
      case 10:
        return (pc(t.type._context), null);
      case 22:
      case 23:
        return (Yc(), null);
      case 24:
        return null;
      default:
        return null;
    }
  }
  var Jo = !1,
    Je = !1,
    Ev = typeof WeakSet == "function" ? WeakSet : Set,
    D = null;
  function gs(e, t) {
    var r = e.ref;
    if (r !== null)
      if (typeof r == "function")
        try {
          r(null);
        } catch (n) {
          je(e, t, n);
        }
      else r.current = null;
  }
  function $c(e, t, r) {
    try {
      r();
    } catch (n) {
      je(e, t, n);
    }
  }
  var mf = !1;
  function Tv(e, t) {
    if (((Yl = vo), (e = qp()), Bl(e))) {
      if ("selectionStart" in e)
        var r = { start: e.selectionStart, end: e.selectionEnd };
      else
        e: {
          r = ((r = e.ownerDocument) && r.defaultView) || window;
          var n = r.getSelection && r.getSelection();
          if (n && n.rangeCount !== 0) {
            r = n.anchorNode;
            var s = n.anchorOffset,
              a = n.focusNode;
            n = n.focusOffset;
            try {
              (r.nodeType, a.nodeType);
            } catch {
              r = null;
              break e;
            }
            var o = 0,
              l = -1,
              c = -1,
              u = 0,
              d = 0,
              f = e,
              m = null;
            t: for (;;) {
              for (
                var v;
                f !== r || (s !== 0 && f.nodeType !== 3) || (l = o + s),
                  f !== a || (n !== 0 && f.nodeType !== 3) || (c = o + n),
                  f.nodeType === 3 && (o += f.nodeValue.length),
                  (v = f.firstChild) !== null;
              )
                ((m = f), (f = v));
              for (;;) {
                if (f === e) break t;
                if (
                  (m === r && ++u === s && (l = o),
                  m === a && ++d === n && (c = o),
                  (v = f.nextSibling) !== null)
                )
                  break;
                ((f = m), (m = f.parentNode));
              }
              f = v;
            }
            r = l === -1 || c === -1 ? null : { start: l, end: c };
          } else r = null;
        }
      r = r || { start: 0, end: 0 };
    } else r = null;
    for (
      Jl = { focusedElem: e, selectionRange: r }, vo = !1, D = t;
      D !== null;
    )
      if (((t = D), (e = t.child), (t.subtreeFlags & 1028) !== 0 && e !== null))
        ((e.return = t), (D = e));
      else
        for (; D !== null; ) {
          t = D;
          try {
            var x = t.alternate;
            if ((t.flags & 1024) !== 0)
              switch (t.tag) {
                case 0:
                case 11:
                case 15:
                  break;
                case 1:
                  if (x !== null) {
                    var b = x.memoizedProps,
                      j = x.memoizedState,
                      g = t.stateNode,
                      p = g.getSnapshotBeforeUpdate(
                        t.elementType === t.type ? b : $t(t.type, b),
                        j
                      );
                    g.__reactInternalSnapshotBeforeUpdate = p;
                  }
                  break;
                case 3:
                  var y = t.stateNode.containerInfo;
                  y.nodeType === 1
                    ? (y.textContent = "")
                    : y.nodeType === 9 &&
                      y.documentElement &&
                      y.removeChild(y.documentElement);
                  break;
                case 5:
                case 6:
                case 4:
                case 17:
                  break;
                default:
                  throw Error(E(163));
              }
          } catch (w) {
            je(t, t.return, w);
          }
          if (((e = t.sibling), e !== null)) {
            ((e.return = t.return), (D = e));
            break;
          }
          D = t.return;
        }
    return ((x = mf), (mf = !1), x);
  }
  function Aa(e, t, r) {
    var n = t.updateQueue;
    if (((n = n !== null ? n.lastEffect : null), n !== null)) {
      var s = (n = n.next);
      do {
        if ((s.tag & e) === e) {
          var a = s.destroy;
          ((s.destroy = void 0), a !== void 0 && $c(t, r, a));
        }
        s = s.next;
      } while (s !== n);
    }
  }
  function Xo(e, t) {
    if (
      ((t = t.updateQueue), (t = t !== null ? t.lastEffect : null), t !== null)
    ) {
      var r = (t = t.next);
      do {
        if ((r.tag & e) === e) {
          var n = r.create;
          r.destroy = n();
        }
        r = r.next;
      } while (r !== t);
    }
  }
  function Fc(e) {
    var t = e.ref;
    if (t !== null) {
      var r = e.stateNode;
      (e.tag, (e = r), typeof t == "function" ? t(e) : (t.current = e));
    }
  }
  function ff(e) {
    var t = e.alternate;
    (t !== null && ((e.alternate = null), ff(t)),
      (e.child = null),
      (e.deletions = null),
      (e.sibling = null),
      e.tag === 5 &&
        ((t = e.stateNode),
        t !== null &&
          (delete t[Ht],
          delete t[va],
          delete t[rc],
          delete t[mv],
          delete t[fv])),
      (e.stateNode = null),
      (e.return = null),
      (e.dependencies = null),
      (e.memoizedProps = null),
      (e.memoizedState = null),
      (e.pendingProps = null),
      (e.stateNode = null),
      (e.updateQueue = null));
  }
  function hf(e) {
    return e.tag === 5 || e.tag === 3 || e.tag === 4;
  }
  function gf(e) {
    e: for (;;) {
      for (; e.sibling === null; ) {
        if (e.return === null || hf(e.return)) return null;
        e = e.return;
      }
      for (
        e.sibling.return = e.return, e = e.sibling;
        e.tag !== 5 && e.tag !== 6 && e.tag !== 18;
      ) {
        if (e.flags & 2 || e.child === null || e.tag === 4) continue e;
        ((e.child.return = e), (e = e.child));
      }
      if (!(e.flags & 2)) return e.stateNode;
    }
  }
  function Uc(e, t, r) {
    var n = e.tag;
    if (n === 5 || n === 6)
      ((e = e.stateNode),
        t
          ? r.nodeType === 8
            ? r.parentNode.insertBefore(e, t)
            : r.insertBefore(e, t)
          : (r.nodeType === 8
              ? ((t = r.parentNode), t.insertBefore(e, r))
              : ((t = r), t.appendChild(e)),
            (r = r._reactRootContainer),
            r != null || t.onclick !== null || (t.onclick = Eo)));
    else if (n !== 4 && ((e = e.child), e !== null))
      for (Uc(e, t, r), e = e.sibling; e !== null; )
        (Uc(e, t, r), (e = e.sibling));
  }
  function Zc(e, t, r) {
    var n = e.tag;
    if (n === 5 || n === 6)
      ((e = e.stateNode), t ? r.insertBefore(e, t) : r.appendChild(e));
    else if (n !== 4 && ((e = e.child), e !== null))
      for (Zc(e, t, r), e = e.sibling; e !== null; )
        (Zc(e, t, r), (e = e.sibling));
  }
  var Ke = null,
    Ft = !1;
  function $r(e, t, r) {
    for (r = r.child; r !== null; ) (yf(e, t, r), (r = r.sibling));
  }
  function yf(e, t, r) {
    if (Wt && typeof Wt.onCommitFiberUnmount == "function")
      try {
        Wt.onCommitFiberUnmount(po, r);
      } catch {}
    switch (r.tag) {
      case 5:
        Je || gs(r, t);
      case 6:
        var n = Ke,
          s = Ft;
        ((Ke = null),
          $r(e, t, r),
          (Ke = n),
          (Ft = s),
          Ke !== null &&
            (Ft
              ? ((e = Ke),
                (r = r.stateNode),
                e.nodeType === 8
                  ? e.parentNode.removeChild(r)
                  : e.removeChild(r))
              : Ke.removeChild(r.stateNode)));
        break;
      case 18:
        Ke !== null &&
          (Ft
            ? ((e = Ke),
              (r = r.stateNode),
              e.nodeType === 8
                ? tc(e.parentNode, r)
                : e.nodeType === 1 && tc(e, r),
              ia(e))
            : tc(Ke, r.stateNode));
        break;
      case 4:
        ((n = Ke),
          (s = Ft),
          (Ke = r.stateNode.containerInfo),
          (Ft = !0),
          $r(e, t, r),
          (Ke = n),
          (Ft = s));
        break;
      case 0:
      case 11:
      case 14:
      case 15:
        if (
          !Je &&
          ((n = r.updateQueue), n !== null && ((n = n.lastEffect), n !== null))
        ) {
          s = n = n.next;
          do {
            var a = s,
              o = a.destroy;
            ((a = a.tag),
              o !== void 0 && ((a & 2) !== 0 || (a & 4) !== 0) && $c(r, t, o),
              (s = s.next));
          } while (s !== n);
        }
        $r(e, t, r);
        break;
      case 1:
        if (
          !Je &&
          (gs(r, t),
          (n = r.stateNode),
          typeof n.componentWillUnmount == "function")
        )
          try {
            ((n.props = r.memoizedProps),
              (n.state = r.memoizedState),
              n.componentWillUnmount());
          } catch (l) {
            je(r, t, l);
          }
        $r(e, t, r);
        break;
      case 21:
        $r(e, t, r);
        break;
      case 22:
        r.mode & 1
          ? ((Je = (n = Je) || r.memoizedState !== null), $r(e, t, r), (Je = n))
          : $r(e, t, r);
        break;
      default:
        $r(e, t, r);
    }
  }
  function vf(e) {
    var t = e.updateQueue;
    if (t !== null) {
      e.updateQueue = null;
      var r = e.stateNode;
      (r === null && (r = e.stateNode = new Ev()),
        t.forEach(function (n) {
          var s = Fv.bind(null, e, n);
          r.has(n) || (r.add(n), n.then(s, s));
        }));
    }
  }
  function Ut(e, t) {
    var r = t.deletions;
    if (r !== null)
      for (var n = 0; n < r.length; n++) {
        var s = r[n];
        try {
          var a = e,
            o = t,
            l = o;
          e: for (; l !== null; ) {
            switch (l.tag) {
              case 5:
                ((Ke = l.stateNode), (Ft = !1));
                break e;
              case 3:
                ((Ke = l.stateNode.containerInfo), (Ft = !0));
                break e;
              case 4:
                ((Ke = l.stateNode.containerInfo), (Ft = !0));
                break e;
            }
            l = l.return;
          }
          if (Ke === null) throw Error(E(160));
          (yf(a, o, s), (Ke = null), (Ft = !1));
          var c = s.alternate;
          (c !== null && (c.return = null), (s.return = null));
        } catch (u) {
          je(s, t, u);
        }
      }
    if (t.subtreeFlags & 12854)
      for (t = t.child; t !== null; ) (xf(t, e), (t = t.sibling));
  }
  function xf(e, t) {
    var r = e.alternate,
      n = e.flags;
    switch (e.tag) {
      case 0:
      case 11:
      case 14:
      case 15:
        if ((Ut(t, e), Yt(e), n & 4)) {
          try {
            (Aa(3, e, e.return), Xo(3, e));
          } catch (b) {
            je(e, e.return, b);
          }
          try {
            Aa(5, e, e.return);
          } catch (b) {
            je(e, e.return, b);
          }
        }
        break;
      case 1:
        (Ut(t, e), Yt(e), n & 512 && r !== null && gs(r, r.return));
        break;
      case 5:
        if (
          (Ut(t, e),
          Yt(e),
          n & 512 && r !== null && gs(r, r.return),
          e.flags & 32)
        ) {
          var s = e.stateNode;
          try {
            Gs(s, "");
          } catch (b) {
            je(e, e.return, b);
          }
        }
        if (n & 4 && ((s = e.stateNode), s != null)) {
          var a = e.memoizedProps,
            o = r !== null ? r.memoizedProps : a,
            l = e.type,
            c = e.updateQueue;
          if (((e.updateQueue = null), c !== null))
            try {
              (l === "input" &&
                a.type === "radio" &&
                a.name != null &&
                Kd(s, a),
                xl(l, o));
              var u = xl(l, a);
              for (o = 0; o < c.length; o += 2) {
                var d = c[o],
                  f = c[o + 1];
                d === "style"
                  ? ep(s, f)
                  : d === "dangerouslySetInnerHTML"
                    ? Jd(s, f)
                    : d === "children"
                      ? Gs(s, f)
                      : rl(s, d, f, u);
              }
              switch (l) {
                case "input":
                  fl(s, a);
                  break;
                case "textarea":
                  Qd(s, a);
                  break;
                case "select":
                  var m = s._wrapperState.wasMultiple;
                  s._wrapperState.wasMultiple = !!a.multiple;
                  var v = a.value;
                  v != null
                    ? Qn(s, !!a.multiple, v, !1)
                    : m !== !!a.multiple &&
                      (a.defaultValue != null
                        ? Qn(s, !!a.multiple, a.defaultValue, !0)
                        : Qn(s, !!a.multiple, a.multiple ? [] : "", !1));
              }
              s[va] = a;
            } catch (b) {
              je(e, e.return, b);
            }
        }
        break;
      case 6:
        if ((Ut(t, e), Yt(e), n & 4)) {
          if (e.stateNode === null) throw Error(E(162));
          ((s = e.stateNode), (a = e.memoizedProps));
          try {
            s.nodeValue = a;
          } catch (b) {
            je(e, e.return, b);
          }
        }
        break;
      case 3:
        if (
          (Ut(t, e), Yt(e), n & 4 && r !== null && r.memoizedState.isDehydrated)
        )
          try {
            ia(t.containerInfo);
          } catch (b) {
            je(e, e.return, b);
          }
        break;
      case 4:
        (Ut(t, e), Yt(e));
        break;
      case 13:
        (Ut(t, e),
          Yt(e),
          (s = e.child),
          s.flags & 8192 &&
            ((a = s.memoizedState !== null),
            (s.stateNode.isHidden = a),
            !a ||
              (s.alternate !== null && s.alternate.memoizedState !== null) ||
              (Vc = Pe())),
          n & 4 && vf(e));
        break;
      case 22:
        if (
          ((d = r !== null && r.memoizedState !== null),
          e.mode & 1 ? ((Je = (u = Je) || d), Ut(t, e), (Je = u)) : Ut(t, e),
          Yt(e),
          n & 8192)
        ) {
          if (
            ((u = e.memoizedState !== null),
            (e.stateNode.isHidden = u) && !d && (e.mode & 1) !== 0)
          )
            for (D = e, d = e.child; d !== null; ) {
              for (f = D = d; D !== null; ) {
                switch (((m = D), (v = m.child), m.tag)) {
                  case 0:
                  case 11:
                  case 14:
                  case 15:
                    Aa(4, m, m.return);
                    break;
                  case 1:
                    gs(m, m.return);
                    var x = m.stateNode;
                    if (typeof x.componentWillUnmount == "function") {
                      ((n = m), (r = m.return));
                      try {
                        ((t = n),
                          (x.props = t.memoizedProps),
                          (x.state = t.memoizedState),
                          x.componentWillUnmount());
                      } catch (b) {
                        je(n, r, b);
                      }
                    }
                    break;
                  case 5:
                    gs(m, m.return);
                    break;
                  case 22:
                    if (m.memoizedState !== null) {
                      kf(f);
                      continue;
                    }
                }
                v !== null ? ((v.return = m), (D = v)) : kf(f);
              }
              d = d.sibling;
            }
          e: for (d = null, f = e; ; ) {
            if (f.tag === 5) {
              if (d === null) {
                d = f;
                try {
                  ((s = f.stateNode),
                    u
                      ? ((a = s.style),
                        typeof a.setProperty == "function"
                          ? a.setProperty("display", "none", "important")
                          : (a.display = "none"))
                      : ((l = f.stateNode),
                        (c = f.memoizedProps.style),
                        (o =
                          c != null && c.hasOwnProperty("display")
                            ? c.display
                            : null),
                        (l.style.display = Xd("display", o))));
                } catch (b) {
                  je(e, e.return, b);
                }
              }
            } else if (f.tag === 6) {
              if (d === null)
                try {
                  f.stateNode.nodeValue = u ? "" : f.memoizedProps;
                } catch (b) {
                  je(e, e.return, b);
                }
            } else if (
              ((f.tag !== 22 && f.tag !== 23) ||
                f.memoizedState === null ||
                f === e) &&
              f.child !== null
            ) {
              ((f.child.return = f), (f = f.child));
              continue;
            }
            if (f === e) break e;
            for (; f.sibling === null; ) {
              if (f.return === null || f.return === e) break e;
              (d === f && (d = null), (f = f.return));
            }
            (d === f && (d = null),
              (f.sibling.return = f.return),
              (f = f.sibling));
          }
        }
        break;
      case 19:
        (Ut(t, e), Yt(e), n & 4 && vf(e));
        break;
      case 21:
        break;
      default:
        (Ut(t, e), Yt(e));
    }
  }
  function Yt(e) {
    var t = e.flags;
    if (t & 2) {
      try {
        e: {
          for (var r = e.return; r !== null; ) {
            if (hf(r)) {
              var n = r;
              break e;
            }
            r = r.return;
          }
          throw Error(E(160));
        }
        switch (n.tag) {
          case 5:
            var s = n.stateNode;
            n.flags & 32 && (Gs(s, ""), (n.flags &= -33));
            var a = gf(e);
            Zc(e, a, s);
            break;
          case 3:
          case 4:
            var o = n.stateNode.containerInfo,
              l = gf(e);
            Uc(e, l, o);
            break;
          default:
            throw Error(E(161));
        }
      } catch (c) {
        je(e, e.return, c);
      }
      e.flags &= -3;
    }
    t & 4096 && (e.flags &= -4097);
  }
  function Pv(e, t, r) {
    ((D = e), bf(e));
  }
  function bf(e, t, r) {
    for (var n = (e.mode & 1) !== 0; D !== null; ) {
      var s = D,
        a = s.child;
      if (s.tag === 22 && n) {
        var o = s.memoizedState !== null || Jo;
        if (!o) {
          var l = s.alternate,
            c = (l !== null && l.memoizedState !== null) || Je;
          l = Jo;
          var u = Je;
          if (((Jo = o), (Je = c) && !u))
            for (D = s; D !== null; )
              ((o = D),
                (c = o.child),
                o.tag === 22 && o.memoizedState !== null
                  ? _f(s)
                  : c !== null
                    ? ((c.return = o), (D = c))
                    : _f(s));
          for (; a !== null; ) ((D = a), bf(a), (a = a.sibling));
          ((D = s), (Jo = l), (Je = u));
        }
        wf(e);
      } else
        (s.subtreeFlags & 8772) !== 0 && a !== null
          ? ((a.return = s), (D = a))
          : wf(e);
    }
  }
  function wf(e) {
    for (; D !== null; ) {
      var t = D;
      if ((t.flags & 8772) !== 0) {
        var r = t.alternate;
        try {
          if ((t.flags & 8772) !== 0)
            switch (t.tag) {
              case 0:
              case 11:
              case 15:
                Je || Xo(5, t);
                break;
              case 1:
                var n = t.stateNode;
                if (t.flags & 4 && !Je)
                  if (r === null) n.componentDidMount();
                  else {
                    var s =
                      t.elementType === t.type
                        ? r.memoizedProps
                        : $t(t.type, r.memoizedProps);
                    n.componentDidUpdate(
                      s,
                      r.memoizedState,
                      n.__reactInternalSnapshotBeforeUpdate
                    );
                  }
                var a = t.updateQueue;
                a !== null && wm(t, a, n);
                break;
              case 3:
                var o = t.updateQueue;
                if (o !== null) {
                  if (((r = null), t.child !== null))
                    switch (t.child.tag) {
                      case 5:
                        r = t.child.stateNode;
                        break;
                      case 1:
                        r = t.child.stateNode;
                    }
                  wm(t, o, r);
                }
                break;
              case 5:
                var l = t.stateNode;
                if (r === null && t.flags & 4) {
                  r = l;
                  var c = t.memoizedProps;
                  switch (t.type) {
                    case "button":
                    case "input":
                    case "select":
                    case "textarea":
                      c.autoFocus && r.focus();
                      break;
                    case "img":
                      c.src && (r.src = c.src);
                  }
                }
                break;
              case 6:
                break;
              case 4:
                break;
              case 12:
                break;
              case 13:
                if (t.memoizedState === null) {
                  var u = t.alternate;
                  if (u !== null) {
                    var d = u.memoizedState;
                    if (d !== null) {
                      var f = d.dehydrated;
                      f !== null && ia(f);
                    }
                  }
                }
                break;
              case 19:
              case 17:
              case 21:
              case 22:
              case 23:
              case 25:
                break;
              default:
                throw Error(E(163));
            }
          Je || (t.flags & 512 && Fc(t));
        } catch (m) {
          je(t, t.return, m);
        }
      }
      if (t === e) {
        D = null;
        break;
      }
      if (((r = t.sibling), r !== null)) {
        ((r.return = t.return), (D = r));
        break;
      }
      D = t.return;
    }
  }
  function kf(e) {
    for (; D !== null; ) {
      var t = D;
      if (t === e) {
        D = null;
        break;
      }
      var r = t.sibling;
      if (r !== null) {
        ((r.return = t.return), (D = r));
        break;
      }
      D = t.return;
    }
  }
  function _f(e) {
    for (; D !== null; ) {
      var t = D;
      try {
        switch (t.tag) {
          case 0:
          case 11:
          case 15:
            var r = t.return;
            try {
              Xo(4, t);
            } catch (c) {
              je(t, r, c);
            }
            break;
          case 1:
            var n = t.stateNode;
            if (typeof n.componentDidMount == "function") {
              var s = t.return;
              try {
                n.componentDidMount();
              } catch (c) {
                je(t, s, c);
              }
            }
            var a = t.return;
            try {
              Fc(t);
            } catch (c) {
              je(t, a, c);
            }
            break;
          case 5:
            var o = t.return;
            try {
              Fc(t);
            } catch (c) {
              je(t, o, c);
            }
        }
      } catch (c) {
        je(t, t.return, c);
      }
      if (t === e) {
        D = null;
        break;
      }
      var l = t.sibling;
      if (l !== null) {
        ((l.return = t.return), (D = l));
        break;
      }
      D = t.return;
    }
  }
  var Ov = Math.ceil,
    ei = ar.ReactCurrentDispatcher,
    Bc = ar.ReactCurrentOwner,
    At = ar.ReactCurrentBatchConfig,
    ne = 0,
    Ze = null,
    De = null,
    We = 0,
    kt = 0,
    ys = Ir(0),
    Me = 0,
    Ea = null,
    xn = 0,
    ti = 0,
    qc = 0,
    Ta = null,
    dt = null,
    Vc = 0,
    vs = 1 / 0,
    mr = null,
    ri = !1,
    Kc = null,
    Fr = null,
    ni = !1,
    Ur = null,
    si = 0,
    Pa = 0,
    Wc = null,
    ai = -1,
    oi = 0;
  function nt() {
    return (ne & 6) !== 0 ? Pe() : ai !== -1 ? ai : (ai = Pe());
  }
  function Zr(e) {
    return (e.mode & 1) === 0
      ? 1
      : (ne & 2) !== 0 && We !== 0
        ? We & -We
        : gv.transition !== null
          ? (oi === 0 && (oi = hp()), oi)
          : ((e = ce),
            e !== 0 ||
              ((e = window.event), (e = e === void 0 ? 16 : Sp(e.type))),
            e);
  }
  function Zt(e, t, r, n) {
    if (50 < Pa) throw ((Pa = 0), (Wc = null), Error(E(185)));
    (ra(e, r, n),
      ((ne & 2) === 0 || e !== Ze) &&
        (e === Ze && ((ne & 2) === 0 && (ti |= r), Me === 4 && Br(e, We)),
        pt(e, n),
        r === 1 &&
          ne === 0 &&
          (t.mode & 1) === 0 &&
          ((vs = Pe() + 500), Io && Dr())));
  }
  function pt(e, t) {
    var r = e.callbackNode;
    g0(e, t);
    var n = ho(e, e === Ze ? We : 0);
    if (n === 0)
      (r !== null && pp(r), (e.callbackNode = null), (e.callbackPriority = 0));
    else if (((t = n & -n), e.callbackPriority !== t)) {
      if ((r != null && pp(r), t === 1))
        (e.tag === 0 ? hv(Cf.bind(null, e)) : cm(Cf.bind(null, e)),
          dv(function () {
            (ne & 6) === 0 && Dr();
          }),
          (r = null));
      else {
        switch (gp(n)) {
          case 1:
            r = jl;
            break;
          case 4:
            r = mp;
            break;
          case 16:
            r = uo;
            break;
          case 536870912:
            r = fp;
            break;
          default:
            r = uo;
        }
        r = Of(r, Sf.bind(null, e));
      }
      ((e.callbackPriority = t), (e.callbackNode = r));
    }
  }
  function Sf(e, t) {
    if (((ai = -1), (oi = 0), (ne & 6) !== 0)) throw Error(E(327));
    var r = e.callbackNode;
    if (xs() && e.callbackNode !== r) return null;
    var n = ho(e, e === Ze ? We : 0);
    if (n === 0) return null;
    if ((n & 30) !== 0 || (n & e.expiredLanes) !== 0 || t) t = ii(e, n);
    else {
      t = n;
      var s = ne;
      ne |= 2;
      var a = Nf();
      (Ze !== e || We !== t) && ((mr = null), (vs = Pe() + 500), wn(e, t));
      do
        try {
          Dv();
          break;
        } catch (l) {
          jf(e, l);
        }
      while (!0);
      (dc(),
        (ei.current = a),
        (ne = s),
        De !== null ? (t = 0) : ((Ze = null), (We = 0), (t = Me)));
    }
    if (t !== 0) {
      if (
        (t === 2 && ((s = Nl(e)), s !== 0 && ((n = s), (t = Hc(e, s)))),
        t === 1)
      )
        throw ((r = Ea), wn(e, 0), Br(e, n), pt(e, Pe()), r);
      if (t === 6) Br(e, n);
      else {
        if (
          ((s = e.current.alternate),
          (n & 30) === 0 &&
            !Iv(s) &&
            ((t = ii(e, n)),
            t === 2 && ((a = Nl(e)), a !== 0 && ((n = a), (t = Hc(e, a)))),
            t === 1))
        )
          throw ((r = Ea), wn(e, 0), Br(e, n), pt(e, Pe()), r);
        switch (((e.finishedWork = s), (e.finishedLanes = n), t)) {
          case 0:
          case 1:
            throw Error(E(345));
          case 2:
            kn(e, dt, mr);
            break;
          case 3:
            if (
              (Br(e, n),
              (n & 130023424) === n && ((t = Vc + 500 - Pe()), 10 < t))
            ) {
              if (ho(e, 0) !== 0) break;
              if (((s = e.suspendedLanes), (s & n) !== n)) {
                (nt(), (e.pingedLanes |= e.suspendedLanes & s));
                break;
              }
              e.timeoutHandle = ec(kn.bind(null, e, dt, mr), t);
              break;
            }
            kn(e, dt, mr);
            break;
          case 4:
            if ((Br(e, n), (n & 4194240) === n)) break;
            for (t = e.eventTimes, s = -1; 0 < n; ) {
              var o = 31 - Dt(n);
              ((a = 1 << o), (o = t[o]), o > s && (s = o), (n &= ~a));
            }
            if (
              ((n = s),
              (n = Pe() - n),
              (n =
                (120 > n
                  ? 120
                  : 480 > n
                    ? 480
                    : 1080 > n
                      ? 1080
                      : 1920 > n
                        ? 1920
                        : 3e3 > n
                          ? 3e3
                          : 4320 > n
                            ? 4320
                            : 1960 * Ov(n / 1960)) - n),
              10 < n)
            ) {
              e.timeoutHandle = ec(kn.bind(null, e, dt, mr), n);
              break;
            }
            kn(e, dt, mr);
            break;
          case 5:
            kn(e, dt, mr);
            break;
          default:
            throw Error(E(329));
        }
      }
    }
    return (pt(e, Pe()), e.callbackNode === r ? Sf.bind(null, e) : null);
  }
  function Hc(e, t) {
    var r = Ta;
    return (
      e.current.memoizedState.isDehydrated && (wn(e, t).flags |= 256),
      (e = ii(e, t)),
      e !== 2 && ((t = dt), (dt = r), t !== null && Qc(t)),
      e
    );
  }
  function Qc(e) {
    dt === null ? (dt = e) : dt.push.apply(dt, e);
  }
  function Iv(e) {
    for (var t = e; ; ) {
      if (t.flags & 16384) {
        var r = t.updateQueue;
        if (r !== null && ((r = r.stores), r !== null))
          for (var n = 0; n < r.length; n++) {
            var s = r[n],
              a = s.getSnapshot;
            s = s.value;
            try {
              if (!zt(a(), s)) return !1;
            } catch {
              return !1;
            }
          }
      }
      if (((r = t.child), t.subtreeFlags & 16384 && r !== null))
        ((r.return = t), (t = r));
      else {
        if (t === e) break;
        for (; t.sibling === null; ) {
          if (t.return === null || t.return === e) return !0;
          t = t.return;
        }
        ((t.sibling.return = t.return), (t = t.sibling));
      }
    }
    return !0;
  }
  function Br(e, t) {
    for (
      t &= ~qc,
        t &= ~ti,
        e.suspendedLanes |= t,
        e.pingedLanes &= ~t,
        e = e.expirationTimes;
      0 < t;
    ) {
      var r = 31 - Dt(t),
        n = 1 << r;
      ((e[r] = -1), (t &= ~n));
    }
  }
  function Cf(e) {
    if ((ne & 6) !== 0) throw Error(E(327));
    xs();
    var t = ho(e, 0);
    if ((t & 1) === 0) return (pt(e, Pe()), null);
    var r = ii(e, t);
    if (e.tag !== 0 && r === 2) {
      var n = Nl(e);
      n !== 0 && ((t = n), (r = Hc(e, n)));
    }
    if (r === 1) throw ((r = Ea), wn(e, 0), Br(e, t), pt(e, Pe()), r);
    if (r === 6) throw Error(E(345));
    return (
      (e.finishedWork = e.current.alternate),
      (e.finishedLanes = t),
      kn(e, dt, mr),
      pt(e, Pe()),
      null
    );
  }
  function Gc(e, t) {
    var r = ne;
    ne |= 1;
    try {
      return e(t);
    } finally {
      ((ne = r), ne === 0 && ((vs = Pe() + 500), Io && Dr()));
    }
  }
  function bn(e) {
    Ur !== null && Ur.tag === 0 && (ne & 6) === 0 && xs();
    var t = ne;
    ne |= 1;
    var r = At.transition,
      n = ce;
    try {
      if (((At.transition = null), (ce = 1), e)) return e();
    } finally {
      ((ce = n), (At.transition = r), (ne = t), (ne & 6) === 0 && Dr());
    }
  }
  function Yc() {
    ((kt = ys.current), ge(ys));
  }
  function wn(e, t) {
    ((e.finishedWork = null), (e.finishedLanes = 0));
    var r = e.timeoutHandle;
    if ((r !== -1 && ((e.timeoutHandle = -1), uv(r)), De !== null))
      for (r = De.return; r !== null; ) {
        var n = r;
        switch ((oc(n), n.tag)) {
          case 1:
            ((n = n.type.childContextTypes), n != null && Po());
            break;
          case 3:
            (fs(), ge(lt), ge(Qe), xc());
            break;
          case 5:
            yc(n);
            break;
          case 4:
            fs();
            break;
          case 13:
            ge(we);
            break;
          case 19:
            ge(we);
            break;
          case 10:
            pc(n.type._context);
            break;
          case 22:
          case 23:
            Yc();
        }
        r = r.return;
      }
    if (
      ((Ze = e),
      (De = e = qr(e.current, null)),
      (We = kt = t),
      (Me = 0),
      (Ea = null),
      (qc = ti = xn = 0),
      (dt = Ta = null),
      gn !== null)
    ) {
      for (t = 0; t < gn.length; t++)
        if (((r = gn[t]), (n = r.interleaved), n !== null)) {
          r.interleaved = null;
          var s = n.next,
            a = r.pending;
          if (a !== null) {
            var o = a.next;
            ((a.next = s), (n.next = o));
          }
          r.pending = n;
        }
      gn = null;
    }
    return e;
  }
  function jf(e, t) {
    do {
      var r = De;
      try {
        if ((dc(), (qo.current = Ho), Vo)) {
          for (var n = ke.memoizedState; n !== null; ) {
            var s = n.queue;
            (s !== null && (s.pending = null), (n = n.next));
          }
          Vo = !1;
        }
        if (
          ((vn = 0),
          (Ue = ze = ke = null),
          (Sa = !1),
          (Ca = 0),
          (Bc.current = null),
          r === null || r.return === null)
        ) {
          ((Me = 1), (Ea = t), (De = null));
          break;
        }
        e: {
          var a = e,
            o = r.return,
            l = r,
            c = t;
          if (
            ((t = We),
            (l.flags |= 32768),
            c !== null && typeof c == "object" && typeof c.then == "function")
          ) {
            var u = c,
              d = l,
              f = d.tag;
            if ((d.mode & 1) === 0 && (f === 0 || f === 11 || f === 15)) {
              var m = d.alternate;
              m
                ? ((d.updateQueue = m.updateQueue),
                  (d.memoizedState = m.memoizedState),
                  (d.lanes = m.lanes))
                : ((d.updateQueue = null), (d.memoizedState = null));
            }
            var v = Gm(o);
            if (v !== null) {
              ((v.flags &= -257),
                Ym(v, o, l, a, t),
                v.mode & 1 && Qm(a, u, t),
                (t = v),
                (c = u));
              var x = t.updateQueue;
              if (x === null) {
                var b = new Set();
                (b.add(c), (t.updateQueue = b));
              } else x.add(c);
              break e;
            } else {
              if ((t & 1) === 0) {
                (Qm(a, u, t), Jc());
                break e;
              }
              c = Error(E(426));
            }
          } else if (ye && l.mode & 1) {
            var j = Gm(o);
            if (j !== null) {
              ((j.flags & 65536) === 0 && (j.flags |= 256),
                Ym(j, o, l, a, t),
                cc(hs(c, l)));
              break e;
            }
          }
          ((a = c = hs(c, l)),
            Me !== 4 && (Me = 2),
            Ta === null ? (Ta = [a]) : Ta.push(a),
            (a = o));
          do {
            switch (a.tag) {
              case 3:
                ((a.flags |= 65536), (t &= -t), (a.lanes |= t));
                var g = Wm(a, c, t);
                bm(a, g);
                break e;
              case 1:
                l = c;
                var p = a.type,
                  y = a.stateNode;
                if (
                  (a.flags & 128) === 0 &&
                  (typeof p.getDerivedStateFromError == "function" ||
                    (y !== null &&
                      typeof y.componentDidCatch == "function" &&
                      (Fr === null || !Fr.has(y))))
                ) {
                  ((a.flags |= 65536), (t &= -t), (a.lanes |= t));
                  var w = Hm(a, l, t);
                  bm(a, w);
                  break e;
                }
            }
            a = a.return;
          } while (a !== null);
        }
        Af(r);
      } catch (S) {
        ((t = S), De === r && r !== null && (De = r = r.return));
        continue;
      }
      break;
    } while (!0);
  }
  function Nf() {
    var e = ei.current;
    return ((ei.current = Ho), e === null ? Ho : e);
  }
  function Jc() {
    ((Me === 0 || Me === 3 || Me === 2) && (Me = 4),
      Ze === null ||
        ((xn & 268435455) === 0 && (ti & 268435455) === 0) ||
        Br(Ze, We));
  }
  function ii(e, t) {
    var r = ne;
    ne |= 2;
    var n = Nf();
    (Ze !== e || We !== t) && ((mr = null), wn(e, t));
    do
      try {
        Lv();
        break;
      } catch (s) {
        jf(e, s);
      }
    while (!0);
    if ((dc(), (ne = r), (ei.current = n), De !== null)) throw Error(E(261));
    return ((Ze = null), (We = 0), Me);
  }
  function Lv() {
    for (; De !== null; ) Rf(De);
  }
  function Dv() {
    for (; De !== null && !i0(); ) Rf(De);
  }
  function Rf(e) {
    var t = Pf(e.alternate, e, kt);
    ((e.memoizedProps = e.pendingProps),
      t === null ? Af(e) : (De = t),
      (Bc.current = null));
  }
  function Af(e) {
    var t = e;
    do {
      var r = t.alternate;
      if (((e = t.return), (t.flags & 32768) === 0)) {
        if (((r = Rv(r, t, kt)), r !== null)) {
          De = r;
          return;
        }
      } else {
        if (((r = Av(r, t)), r !== null)) {
          ((r.flags &= 32767), (De = r));
          return;
        }
        if (e !== null)
          ((e.flags |= 32768), (e.subtreeFlags = 0), (e.deletions = null));
        else {
          ((Me = 6), (De = null));
          return;
        }
      }
      if (((t = t.sibling), t !== null)) {
        De = t;
        return;
      }
      De = t = e;
    } while (t !== null);
    Me === 0 && (Me = 5);
  }
  function kn(e, t, r) {
    var n = ce,
      s = At.transition;
    try {
      ((At.transition = null), (ce = 1), zv(e, t, r, n));
    } finally {
      ((At.transition = s), (ce = n));
    }
    return null;
  }
  function zv(e, t, r, n) {
    do xs();
    while (Ur !== null);
    if ((ne & 6) !== 0) throw Error(E(327));
    r = e.finishedWork;
    var s = e.finishedLanes;
    if (r === null) return null;
    if (((e.finishedWork = null), (e.finishedLanes = 0), r === e.current))
      throw Error(E(177));
    ((e.callbackNode = null), (e.callbackPriority = 0));
    var a = r.lanes | r.childLanes;
    if (
      (y0(e, a),
      e === Ze && ((De = Ze = null), (We = 0)),
      ((r.subtreeFlags & 2064) === 0 && (r.flags & 2064) === 0) ||
        ni ||
        ((ni = !0),
        Of(uo, function () {
          return (xs(), null);
        })),
      (a = (r.flags & 15990) !== 0),
      (r.subtreeFlags & 15990) !== 0 || a)
    ) {
      ((a = At.transition), (At.transition = null));
      var o = ce;
      ce = 1;
      var l = ne;
      ((ne |= 4),
        (Bc.current = null),
        Tv(e, r),
        xf(r, e),
        nv(Jl),
        (vo = !!Yl),
        (Jl = Yl = null),
        (e.current = r),
        Pv(r),
        l0(),
        (ne = l),
        (ce = o),
        (At.transition = a));
    } else e.current = r;
    if (
      (ni && ((ni = !1), (Ur = e), (si = s)),
      (a = e.pendingLanes),
      a === 0 && (Fr = null),
      d0(r.stateNode),
      pt(e, Pe()),
      t !== null)
    )
      for (n = e.onRecoverableError, r = 0; r < t.length; r++)
        ((s = t[r]), n(s.value, { componentStack: s.stack, digest: s.digest }));
    if (ri) throw ((ri = !1), (e = Kc), (Kc = null), e);
    return (
      (si & 1) !== 0 && e.tag !== 0 && xs(),
      (a = e.pendingLanes),
      (a & 1) !== 0 ? (e === Wc ? Pa++ : ((Pa = 0), (Wc = e))) : (Pa = 0),
      Dr(),
      null
    );
  }
  function xs() {
    if (Ur !== null) {
      var e = gp(si),
        t = At.transition,
        r = ce;
      try {
        if (((At.transition = null), (ce = 16 > e ? 16 : e), Ur === null))
          var n = !1;
        else {
          if (((e = Ur), (Ur = null), (si = 0), (ne & 6) !== 0))
            throw Error(E(331));
          var s = ne;
          for (ne |= 4, D = e.current; D !== null; ) {
            var a = D,
              o = a.child;
            if ((D.flags & 16) !== 0) {
              var l = a.deletions;
              if (l !== null) {
                for (var c = 0; c < l.length; c++) {
                  var u = l[c];
                  for (D = u; D !== null; ) {
                    var d = D;
                    switch (d.tag) {
                      case 0:
                      case 11:
                      case 15:
                        Aa(8, d, a);
                    }
                    var f = d.child;
                    if (f !== null) ((f.return = d), (D = f));
                    else
                      for (; D !== null; ) {
                        d = D;
                        var m = d.sibling,
                          v = d.return;
                        if ((ff(d), d === u)) {
                          D = null;
                          break;
                        }
                        if (m !== null) {
                          ((m.return = v), (D = m));
                          break;
                        }
                        D = v;
                      }
                  }
                }
                var x = a.alternate;
                if (x !== null) {
                  var b = x.child;
                  if (b !== null) {
                    x.child = null;
                    do {
                      var j = b.sibling;
                      ((b.sibling = null), (b = j));
                    } while (b !== null);
                  }
                }
                D = a;
              }
            }
            if ((a.subtreeFlags & 2064) !== 0 && o !== null)
              ((o.return = a), (D = o));
            else
              e: for (; D !== null; ) {
                if (((a = D), (a.flags & 2048) !== 0))
                  switch (a.tag) {
                    case 0:
                    case 11:
                    case 15:
                      Aa(9, a, a.return);
                  }
                var g = a.sibling;
                if (g !== null) {
                  ((g.return = a.return), (D = g));
                  break e;
                }
                D = a.return;
              }
          }
          var p = e.current;
          for (D = p; D !== null; ) {
            o = D;
            var y = o.child;
            if ((o.subtreeFlags & 2064) !== 0 && y !== null)
              ((y.return = o), (D = y));
            else
              e: for (o = p; D !== null; ) {
                if (((l = D), (l.flags & 2048) !== 0))
                  try {
                    switch (l.tag) {
                      case 0:
                      case 11:
                      case 15:
                        Xo(9, l);
                    }
                  } catch (S) {
                    je(l, l.return, S);
                  }
                if (l === o) {
                  D = null;
                  break e;
                }
                var w = l.sibling;
                if (w !== null) {
                  ((w.return = l.return), (D = w));
                  break e;
                }
                D = l.return;
              }
          }
          if (
            ((ne = s),
            Dr(),
            Wt && typeof Wt.onPostCommitFiberRoot == "function")
          )
            try {
              Wt.onPostCommitFiberRoot(po, e);
            } catch {}
          n = !0;
        }
        return n;
      } finally {
        ((ce = r), (At.transition = t));
      }
    }
    return !1;
  }
  function Ef(e, t, r) {
    ((t = hs(r, t)),
      (t = Wm(e, t, 1)),
      (e = Mr(e, t, 1)),
      (t = nt()),
      e !== null && (ra(e, 1, t), pt(e, t)));
  }
  function je(e, t, r) {
    if (e.tag === 3) Ef(e, e, r);
    else
      for (; t !== null; ) {
        if (t.tag === 3) {
          Ef(t, e, r);
          break;
        } else if (t.tag === 1) {
          var n = t.stateNode;
          if (
            typeof t.type.getDerivedStateFromError == "function" ||
            (typeof n.componentDidCatch == "function" &&
              (Fr === null || !Fr.has(n)))
          ) {
            ((e = hs(r, e)),
              (e = Hm(t, e, 1)),
              (t = Mr(t, e, 1)),
              (e = nt()),
              t !== null && (ra(t, 1, e), pt(t, e)));
            break;
          }
        }
        t = t.return;
      }
  }
  function Mv(e, t, r) {
    var n = e.pingCache;
    (n !== null && n.delete(t),
      (t = nt()),
      (e.pingedLanes |= e.suspendedLanes & r),
      Ze === e &&
        (We & r) === r &&
        (Me === 4 || (Me === 3 && (We & 130023424) === We && 500 > Pe() - Vc)
          ? wn(e, 0)
          : (qc |= r)),
      pt(e, t));
  }
  function Tf(e, t) {
    t === 0 &&
      ((e.mode & 1) === 0
        ? (t = 1)
        : ((t = fo), (fo <<= 1), (fo & 130023424) === 0 && (fo = 4194304)));
    var r = nt();
    ((e = ur(e, t)), e !== null && (ra(e, t, r), pt(e, r)));
  }
  function $v(e) {
    var t = e.memoizedState,
      r = 0;
    (t !== null && (r = t.retryLane), Tf(e, r));
  }
  function Fv(e, t) {
    var r = 0;
    switch (e.tag) {
      case 13:
        var n = e.stateNode,
          s = e.memoizedState;
        s !== null && (r = s.retryLane);
        break;
      case 19:
        n = e.stateNode;
        break;
      default:
        throw Error(E(314));
    }
    (n !== null && n.delete(t), Tf(e, r));
  }
  var Pf;
  Pf = function (e, t, r) {
    if (e !== null)
      if (e.memoizedProps !== t.pendingProps || lt.current) ut = !0;
      else {
        if ((e.lanes & r) === 0 && (t.flags & 128) === 0)
          return ((ut = !1), Nv(e, t, r));
        ut = (e.flags & 131072) !== 0;
      }
    else ((ut = !1), ye && (t.flags & 1048576) !== 0 && um(t, Do, t.index));
    switch (((t.lanes = 0), t.tag)) {
      case 2:
        var n = t.type;
        (Yo(e, t), (e = t.pendingProps));
        var s = is(t, Qe.current);
        (ms(t, r), (s = kc(null, t, n, e, s, r)));
        var a = _c();
        return (
          (t.flags |= 1),
          typeof s == "object" &&
          s !== null &&
          typeof s.render == "function" &&
          s.$$typeof === void 0
            ? ((t.tag = 1),
              (t.memoizedState = null),
              (t.updateQueue = null),
              ct(n) ? ((a = !0), Oo(t)) : (a = !1),
              (t.memoizedState =
                s.state !== null && s.state !== void 0 ? s.state : null),
              hc(t),
              (s.updater = Qo),
              (t.stateNode = s),
              (s._reactInternals = t),
              Ac(t, n, e, r),
              (t = Oc(null, t, n, !0, a, r)))
            : ((t.tag = 0), ye && a && ac(t), rt(null, t, s, r), (t = t.child)),
          t
        );
      case 16:
        n = t.elementType;
        e: {
          switch (
            (Yo(e, t),
            (e = t.pendingProps),
            (s = n._init),
            (n = s(n._payload)),
            (t.type = n),
            (s = t.tag = Zv(n)),
            (e = $t(n, e)),
            s)
          ) {
            case 0:
              t = Pc(null, t, n, e, r);
              break e;
            case 1:
              t = nf(null, t, n, e, r);
              break e;
            case 11:
              t = Jm(null, t, n, e, r);
              break e;
            case 14:
              t = Xm(null, t, n, $t(n.type, e), r);
              break e;
          }
          throw Error(E(306, n, ""));
        }
        return t;
      case 0:
        return (
          (n = t.type),
          (s = t.pendingProps),
          (s = t.elementType === n ? s : $t(n, s)),
          Pc(e, t, n, s, r)
        );
      case 1:
        return (
          (n = t.type),
          (s = t.pendingProps),
          (s = t.elementType === n ? s : $t(n, s)),
          nf(e, t, n, s, r)
        );
      case 3:
        e: {
          if ((sf(t), e === null)) throw Error(E(387));
          ((n = t.pendingProps),
            (a = t.memoizedState),
            (s = a.element),
            xm(e, t),
            Zo(t, n, null, r));
          var o = t.memoizedState;
          if (((n = o.element), a.isDehydrated))
            if (
              ((a = {
                element: n,
                isDehydrated: !1,
                cache: o.cache,
                pendingSuspenseBoundaries: o.pendingSuspenseBoundaries,
                transitions: o.transitions,
              }),
              (t.updateQueue.baseState = a),
              (t.memoizedState = a),
              t.flags & 256)
            ) {
              ((s = hs(Error(E(423)), t)), (t = af(e, t, n, r, s)));
              break e;
            } else if (n !== s) {
              ((s = hs(Error(E(424)), t)), (t = af(e, t, n, r, s)));
              break e;
            } else
              for (
                wt = Or(t.stateNode.containerInfo.firstChild),
                  bt = t,
                  ye = !0,
                  Mt = null,
                  r = ym(t, null, n, r),
                  t.child = r;
                r;
              )
                ((r.flags = (r.flags & -3) | 4096), (r = r.sibling));
          else {
            if ((us(), n === s)) {
              t = pr(e, t, r);
              break e;
            }
            rt(e, t, n, r);
          }
          t = t.child;
        }
        return t;
      case 5:
        return (
          km(t),
          e === null && lc(t),
          (n = t.type),
          (s = t.pendingProps),
          (a = e !== null ? e.memoizedProps : null),
          (o = s.children),
          Xl(n, s) ? (o = null) : a !== null && Xl(n, a) && (t.flags |= 32),
          rf(e, t),
          rt(e, t, o, r),
          t.child
        );
      case 6:
        return (e === null && lc(t), null);
      case 13:
        return of(e, t, r);
      case 4:
        return (
          gc(t, t.stateNode.containerInfo),
          (n = t.pendingProps),
          e === null ? (t.child = ds(t, null, n, r)) : rt(e, t, n, r),
          t.child
        );
      case 11:
        return (
          (n = t.type),
          (s = t.pendingProps),
          (s = t.elementType === n ? s : $t(n, s)),
          Jm(e, t, n, s, r)
        );
      case 7:
        return (rt(e, t, t.pendingProps, r), t.child);
      case 8:
        return (rt(e, t, t.pendingProps.children, r), t.child);
      case 12:
        return (rt(e, t, t.pendingProps.children, r), t.child);
      case 10:
        e: {
          if (
            ((n = t.type._context),
            (s = t.pendingProps),
            (a = t.memoizedProps),
            (o = s.value),
            me($o, n._currentValue),
            (n._currentValue = o),
            a !== null)
          )
            if (zt(a.value, o)) {
              if (a.children === s.children && !lt.current) {
                t = pr(e, t, r);
                break e;
              }
            } else
              for (a = t.child, a !== null && (a.return = t); a !== null; ) {
                var l = a.dependencies;
                if (l !== null) {
                  o = a.child;
                  for (var c = l.firstContext; c !== null; ) {
                    if (c.context === n) {
                      if (a.tag === 1) {
                        ((c = dr(-1, r & -r)), (c.tag = 2));
                        var u = a.updateQueue;
                        if (u !== null) {
                          u = u.shared;
                          var d = u.pending;
                          (d === null
                            ? (c.next = c)
                            : ((c.next = d.next), (d.next = c)),
                            (u.pending = c));
                        }
                      }
                      ((a.lanes |= r),
                        (c = a.alternate),
                        c !== null && (c.lanes |= r),
                        mc(a.return, r, t),
                        (l.lanes |= r));
                      break;
                    }
                    c = c.next;
                  }
                } else if (a.tag === 10) o = a.type === t.type ? null : a.child;
                else if (a.tag === 18) {
                  if (((o = a.return), o === null)) throw Error(E(341));
                  ((o.lanes |= r),
                    (l = o.alternate),
                    l !== null && (l.lanes |= r),
                    mc(o, r, t),
                    (o = a.sibling));
                } else o = a.child;
                if (o !== null) o.return = a;
                else
                  for (o = a; o !== null; ) {
                    if (o === t) {
                      o = null;
                      break;
                    }
                    if (((a = o.sibling), a !== null)) {
                      ((a.return = o.return), (o = a));
                      break;
                    }
                    o = o.return;
                  }
                a = o;
              }
          (rt(e, t, s.children, r), (t = t.child));
        }
        return t;
      case 9:
        return (
          (s = t.type),
          (n = t.pendingProps.children),
          ms(t, r),
          (s = Nt(s)),
          (n = n(s)),
          (t.flags |= 1),
          rt(e, t, n, r),
          t.child
        );
      case 14:
        return (
          (n = t.type),
          (s = $t(n, t.pendingProps)),
          (s = $t(n.type, s)),
          Xm(e, t, n, s, r)
        );
      case 15:
        return ef(e, t, t.type, t.pendingProps, r);
      case 17:
        return (
          (n = t.type),
          (s = t.pendingProps),
          (s = t.elementType === n ? s : $t(n, s)),
          Yo(e, t),
          (t.tag = 1),
          ct(n) ? ((e = !0), Oo(t)) : (e = !1),
          ms(t, r),
          Vm(t, n, s),
          Ac(t, n, s, r),
          Oc(null, t, n, !0, e, r)
        );
      case 19:
        return cf(e, t, r);
      case 22:
        return tf(e, t, r);
    }
    throw Error(E(156, t.tag));
  };
  function Of(e, t) {
    return dp(e, t);
  }
  function Uv(e, t, r, n) {
    ((this.tag = e),
      (this.key = r),
      (this.sibling =
        this.child =
        this.return =
        this.stateNode =
        this.type =
        this.elementType =
          null),
      (this.index = 0),
      (this.ref = null),
      (this.pendingProps = t),
      (this.dependencies =
        this.memoizedState =
        this.updateQueue =
        this.memoizedProps =
          null),
      (this.mode = n),
      (this.subtreeFlags = this.flags = 0),
      (this.deletions = null),
      (this.childLanes = this.lanes = 0),
      (this.alternate = null));
  }
  function Et(e, t, r, n) {
    return new Uv(e, t, r, n);
  }
  function Xc(e) {
    return ((e = e.prototype), !(!e || !e.isReactComponent));
  }
  function Zv(e) {
    if (typeof e == "function") return Xc(e) ? 1 : 0;
    if (e != null) {
      if (((e = e.$$typeof), e === al)) return 11;
      if (e === ll) return 14;
    }
    return 2;
  }
  function qr(e, t) {
    var r = e.alternate;
    return (
      r === null
        ? ((r = Et(e.tag, t, e.key, e.mode)),
          (r.elementType = e.elementType),
          (r.type = e.type),
          (r.stateNode = e.stateNode),
          (r.alternate = e),
          (e.alternate = r))
        : ((r.pendingProps = t),
          (r.type = e.type),
          (r.flags = 0),
          (r.subtreeFlags = 0),
          (r.deletions = null)),
      (r.flags = e.flags & 14680064),
      (r.childLanes = e.childLanes),
      (r.lanes = e.lanes),
      (r.child = e.child),
      (r.memoizedProps = e.memoizedProps),
      (r.memoizedState = e.memoizedState),
      (r.updateQueue = e.updateQueue),
      (t = e.dependencies),
      (r.dependencies =
        t === null ? null : { lanes: t.lanes, firstContext: t.firstContext }),
      (r.sibling = e.sibling),
      (r.index = e.index),
      (r.ref = e.ref),
      r
    );
  }
  function li(e, t, r, n, s, a) {
    var o = 2;
    if (((n = e), typeof e == "function")) Xc(e) && (o = 1);
    else if (typeof e == "string") o = 5;
    else
      e: switch (e) {
        case Hn:
          return _n(r.children, s, a, t);
        case nl:
          ((o = 8), (s |= 8));
          break;
        case sl:
          return (
            (e = Et(12, r, t, s | 2)),
            (e.elementType = sl),
            (e.lanes = a),
            e
          );
        case ol:
          return (
            (e = Et(13, r, t, s)),
            (e.elementType = ol),
            (e.lanes = a),
            e
          );
        case il:
          return (
            (e = Et(19, r, t, s)),
            (e.elementType = il),
            (e.lanes = a),
            e
          );
        case Ud:
          return ci(r, s, a, t);
        default:
          if (typeof e == "object" && e !== null)
            switch (e.$$typeof) {
              case $d:
                o = 10;
                break e;
              case Fd:
                o = 9;
                break e;
              case al:
                o = 11;
                break e;
              case ll:
                o = 14;
                break e;
              case Cr:
                ((o = 16), (n = null));
                break e;
            }
          throw Error(E(130, e == null ? e : typeof e, ""));
      }
    return (
      (t = Et(o, r, t, s)),
      (t.elementType = e),
      (t.type = n),
      (t.lanes = a),
      t
    );
  }
  function _n(e, t, r, n) {
    return ((e = Et(7, e, n, t)), (e.lanes = r), e);
  }
  function ci(e, t, r, n) {
    return (
      (e = Et(22, e, n, t)),
      (e.elementType = Ud),
      (e.lanes = r),
      (e.stateNode = { isHidden: !1 }),
      e
    );
  }
  function eu(e, t, r) {
    return ((e = Et(6, e, null, t)), (e.lanes = r), e);
  }
  function tu(e, t, r) {
    return (
      (t = Et(4, e.children !== null ? e.children : [], e.key, t)),
      (t.lanes = r),
      (t.stateNode = {
        containerInfo: e.containerInfo,
        pendingChildren: null,
        implementation: e.implementation,
      }),
      t
    );
  }
  function Bv(e, t, r, n, s) {
    ((this.tag = t),
      (this.containerInfo = e),
      (this.finishedWork =
        this.pingCache =
        this.current =
        this.pendingChildren =
          null),
      (this.timeoutHandle = -1),
      (this.callbackNode = this.pendingContext = this.context = null),
      (this.callbackPriority = 0),
      (this.eventTimes = Rl(0)),
      (this.expirationTimes = Rl(-1)),
      (this.entangledLanes =
        this.finishedLanes =
        this.mutableReadLanes =
        this.expiredLanes =
        this.pingedLanes =
        this.suspendedLanes =
        this.pendingLanes =
          0),
      (this.entanglements = Rl(0)),
      (this.identifierPrefix = n),
      (this.onRecoverableError = s),
      (this.mutableSourceEagerHydrationData = null));
  }
  function ru(e, t, r, n, s, a, o, l, c) {
    return (
      (e = new Bv(e, t, r, l, c)),
      t === 1 ? ((t = 1), a === !0 && (t |= 8)) : (t = 0),
      (a = Et(3, null, null, t)),
      (e.current = a),
      (a.stateNode = e),
      (a.memoizedState = {
        element: n,
        isDehydrated: r,
        cache: null,
        transitions: null,
        pendingSuspenseBoundaries: null,
      }),
      hc(a),
      e
    );
  }
  function qv(e, t, r) {
    var n =
      3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
    return {
      $$typeof: Wn,
      key: n == null ? null : "" + n,
      children: e,
      containerInfo: t,
      implementation: r,
    };
  }
  function If(e) {
    if (!e) return Lr;
    e = e._reactInternals;
    e: {
      if (dn(e) !== e || e.tag !== 1) throw Error(E(170));
      var t = e;
      do {
        switch (t.tag) {
          case 3:
            t = t.stateNode.context;
            break e;
          case 1:
            if (ct(t.type)) {
              t = t.stateNode.__reactInternalMemoizedMergedChildContext;
              break e;
            }
        }
        t = t.return;
      } while (t !== null);
      throw Error(E(171));
    }
    if (e.tag === 1) {
      var r = e.type;
      if (ct(r)) return im(e, r, t);
    }
    return t;
  }
  function Lf(e, t, r, n, s, a, o, l, c) {
    return (
      (e = ru(r, n, !0, e, s, a, o, l, c)),
      (e.context = If(null)),
      (r = e.current),
      (n = nt()),
      (s = Zr(r)),
      (a = dr(n, s)),
      (a.callback = t ?? null),
      Mr(r, a, s),
      (e.current.lanes = s),
      ra(e, s, n),
      pt(e, n),
      e
    );
  }
  function ui(e, t, r, n) {
    var s = t.current,
      a = nt(),
      o = Zr(s);
    return (
      (r = If(r)),
      t.context === null ? (t.context = r) : (t.pendingContext = r),
      (t = dr(a, o)),
      (t.payload = { element: e }),
      (n = n === void 0 ? null : n),
      n !== null && (t.callback = n),
      (e = Mr(s, t, o)),
      e !== null && (Zt(e, s, o, a), Uo(e, s, o)),
      o
    );
  }
  function di(e) {
    return (
      (e = e.current),
      e.child ? (e.child.tag === 5, e.child.stateNode) : null
    );
  }
  function Df(e, t) {
    if (((e = e.memoizedState), e !== null && e.dehydrated !== null)) {
      var r = e.retryLane;
      e.retryLane = r !== 0 && r < t ? r : t;
    }
  }
  function nu(e, t) {
    (Df(e, t), (e = e.alternate) && Df(e, t));
  }
  function Vv() {
    return null;
  }
  var zf =
    typeof reportError == "function"
      ? reportError
      : function (e) {
          console.error(e);
        };
  function su(e) {
    this._internalRoot = e;
  }
  ((pi.prototype.render = su.prototype.render =
    function (e) {
      var t = this._internalRoot;
      if (t === null) throw Error(E(409));
      ui(e, t, null, null);
    }),
    (pi.prototype.unmount = su.prototype.unmount =
      function () {
        var e = this._internalRoot;
        if (e !== null) {
          this._internalRoot = null;
          var t = e.containerInfo;
          (bn(function () {
            ui(null, e, null, null);
          }),
            (t[or] = null));
        }
      }));
  function pi(e) {
    this._internalRoot = e;
  }
  pi.prototype.unstable_scheduleHydration = function (e) {
    if (e) {
      var t = xp();
      e = { blockedOn: null, target: e, priority: t };
      for (var r = 0; r < Er.length && t !== 0 && t < Er[r].priority; r++);
      (Er.splice(r, 0, e), r === 0 && kp(e));
    }
  };
  function au(e) {
    return !(!e || (e.nodeType !== 1 && e.nodeType !== 9 && e.nodeType !== 11));
  }
  function mi(e) {
    return !(
      !e ||
      (e.nodeType !== 1 &&
        e.nodeType !== 9 &&
        e.nodeType !== 11 &&
        (e.nodeType !== 8 || e.nodeValue !== " react-mount-point-unstable "))
    );
  }
  function Mf() {}
  function Kv(e, t, r, n, s) {
    if (s) {
      if (typeof n == "function") {
        var a = n;
        n = function () {
          var u = di(o);
          a.call(u);
        };
      }
      var o = Lf(t, n, e, 0, null, !1, !1, "", Mf);
      return (
        (e._reactRootContainer = o),
        (e[or] = o.current),
        ga(e.nodeType === 8 ? e.parentNode : e),
        bn(),
        o
      );
    }
    for (; (s = e.lastChild); ) e.removeChild(s);
    if (typeof n == "function") {
      var l = n;
      n = function () {
        var u = di(c);
        l.call(u);
      };
    }
    var c = ru(e, 0, !1, null, null, !1, !1, "", Mf);
    return (
      (e._reactRootContainer = c),
      (e[or] = c.current),
      ga(e.nodeType === 8 ? e.parentNode : e),
      bn(function () {
        ui(t, c, r, n);
      }),
      c
    );
  }
  function fi(e, t, r, n, s) {
    var a = r._reactRootContainer;
    if (a) {
      var o = a;
      if (typeof s == "function") {
        var l = s;
        s = function () {
          var c = di(o);
          l.call(c);
        };
      }
      ui(t, o, e, s);
    } else o = Kv(r, t, e, s, n);
    return di(o);
  }
  ((yp = function (e) {
    switch (e.tag) {
      case 3:
        var t = e.stateNode;
        if (t.current.memoizedState.isDehydrated) {
          var r = ta(t.pendingLanes);
          r !== 0 &&
            (Al(t, r | 1),
            pt(t, Pe()),
            (ne & 6) === 0 && ((vs = Pe() + 500), Dr()));
        }
        break;
      case 13:
        (bn(function () {
          var n = ur(e, 1);
          if (n !== null) {
            var s = nt();
            Zt(n, e, 1, s);
          }
        }),
          nu(e, 1));
    }
  }),
    (El = function (e) {
      if (e.tag === 13) {
        var t = ur(e, 134217728);
        if (t !== null) {
          var r = nt();
          Zt(t, e, 134217728, r);
        }
        nu(e, 134217728);
      }
    }),
    (vp = function (e) {
      if (e.tag === 13) {
        var t = Zr(e),
          r = ur(e, t);
        if (r !== null) {
          var n = nt();
          Zt(r, e, t, n);
        }
        nu(e, t);
      }
    }),
    (xp = function () {
      return ce;
    }),
    (bp = function (e, t) {
      var r = ce;
      try {
        return ((ce = e), t());
      } finally {
        ce = r;
      }
    }),
    (kl = function (e, t, r) {
      switch (t) {
        case "input":
          if ((fl(e, r), (t = r.name), r.type === "radio" && t != null)) {
            for (r = e; r.parentNode; ) r = r.parentNode;
            for (
              r = r.querySelectorAll(
                "input[name=" + JSON.stringify("" + t) + '][type="radio"]'
              ),
                t = 0;
              t < r.length;
              t++
            ) {
              var n = r[t];
              if (n !== e && n.form === e.form) {
                var s = To(n);
                if (!s) throw Error(E(90));
                (qd(n), fl(n, s));
              }
            }
          }
          break;
        case "textarea":
          Qd(e, r);
          break;
        case "select":
          ((t = r.value), t != null && Qn(e, !!r.multiple, t, !1));
      }
    }),
    (sp = Gc),
    (ap = bn));
  var Wv = { usingClientEntryPoint: !1, Events: [xa, as, To, rp, np, Gc] },
    Oa = {
      findFiberByHostInstance: pn,
      bundleType: 0,
      version: "18.3.1",
      rendererPackageName: "react-dom",
    },
    Hv = {
      bundleType: Oa.bundleType,
      version: Oa.version,
      rendererPackageName: Oa.rendererPackageName,
      rendererConfig: Oa.rendererConfig,
      overrideHookState: null,
      overrideHookStateDeletePath: null,
      overrideHookStateRenamePath: null,
      overrideProps: null,
      overridePropsDeletePath: null,
      overridePropsRenamePath: null,
      setErrorHandler: null,
      setSuspenseHandler: null,
      scheduleUpdate: null,
      currentDispatcherRef: ar.ReactCurrentDispatcher,
      findHostInstanceByFiber: function (e) {
        return ((e = cp(e)), e === null ? null : e.stateNode);
      },
      findFiberByHostInstance: Oa.findFiberByHostInstance || Vv,
      findHostInstancesForRefresh: null,
      scheduleRefresh: null,
      scheduleRoot: null,
      setRefreshHandler: null,
      getCurrentFiber: null,
      reconcilerVersion: "18.3.1-next-f1338f8080-20240426",
    };
  if (typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u") {
    var hi = __REACT_DEVTOOLS_GLOBAL_HOOK__;
    if (!hi.isDisabled && hi.supportsFiber)
      try {
        ((po = hi.inject(Hv)), (Wt = hi));
      } catch {}
  }
  ((yt.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED = Wv),
    (yt.createPortal = function (e, t) {
      var r =
        2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
      if (!au(t)) throw Error(E(200));
      return qv(e, t, null, r);
    }),
    (yt.createRoot = function (e, t) {
      if (!au(e)) throw Error(E(299));
      var r = !1,
        n = "",
        s = zf;
      return (
        t != null &&
          (t.unstable_strictMode === !0 && (r = !0),
          t.identifierPrefix !== void 0 && (n = t.identifierPrefix),
          t.onRecoverableError !== void 0 && (s = t.onRecoverableError)),
        (t = ru(e, 1, !1, null, null, r, !1, n, s)),
        (e[or] = t.current),
        ga(e.nodeType === 8 ? e.parentNode : e),
        new su(t)
      );
    }),
    (yt.findDOMNode = function (e) {
      if (e == null) return null;
      if (e.nodeType === 1) return e;
      var t = e._reactInternals;
      if (t === void 0)
        throw typeof e.render == "function"
          ? Error(E(188))
          : ((e = Object.keys(e).join(",")), Error(E(268, e)));
      return ((e = cp(t)), (e = e === null ? null : e.stateNode), e);
    }),
    (yt.flushSync = function (e) {
      return bn(e);
    }),
    (yt.hydrate = function (e, t, r) {
      if (!mi(t)) throw Error(E(200));
      return fi(null, e, t, !0, r);
    }),
    (yt.hydrateRoot = function (e, t, r) {
      if (!au(e)) throw Error(E(405));
      var n = (r != null && r.hydratedSources) || null,
        s = !1,
        a = "",
        o = zf;
      if (
        (r != null &&
          (r.unstable_strictMode === !0 && (s = !0),
          r.identifierPrefix !== void 0 && (a = r.identifierPrefix),
          r.onRecoverableError !== void 0 && (o = r.onRecoverableError)),
        (t = Lf(t, null, e, 1, r ?? null, s, !1, a, o)),
        (e[or] = t.current),
        ga(e),
        n)
      )
        for (e = 0; e < n.length; e++)
          ((r = n[e]),
            (s = r._getVersion),
            (s = s(r._source)),
            t.mutableSourceEagerHydrationData == null
              ? (t.mutableSourceEagerHydrationData = [r, s])
              : t.mutableSourceEagerHydrationData.push(r, s));
      return new pi(t);
    }),
    (yt.render = function (e, t, r) {
      if (!mi(t)) throw Error(E(200));
      return fi(null, e, t, !1, r);
    }),
    (yt.unmountComponentAtNode = function (e) {
      if (!mi(e)) throw Error(E(40));
      return e._reactRootContainer
        ? (bn(function () {
            fi(null, null, e, !1, function () {
              ((e._reactRootContainer = null), (e[or] = null));
            });
          }),
          !0)
        : !1;
    }),
    (yt.unstable_batchedUpdates = Gc),
    (yt.unstable_renderSubtreeIntoContainer = function (e, t, r, n) {
      if (!mi(r)) throw Error(E(200));
      if (e == null || e._reactInternals === void 0) throw Error(E(38));
      return fi(e, t, r, !1, n);
    }),
    (yt.version = "18.3.1-next-f1338f8080-20240426"));
  function $f() {
    if (
      !(
        typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" ||
        typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function"
      )
    )
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE($f);
      } catch (e) {
        console.error(e);
      }
  }
  ($f(), (Od.exports = yt));
  var Qv = Od.exports,
    Ff,
    Uf = Qv;
  ((Ff = Uf.createRoot), Uf.hydrateRoot);
  var bs = class {
      constructor() {
        ((this.listeners = new Set()),
          (this.subscribe = this.subscribe.bind(this)));
      }
      subscribe(e) {
        return (
          this.listeners.add(e),
          this.onSubscribe(),
          () => {
            (this.listeners.delete(e), this.onUnsubscribe());
          }
        );
      }
      hasListeners() {
        return this.listeners.size > 0;
      }
      onSubscribe() {}
      onUnsubscribe() {}
    },
    Gv =
      ((cy = class extends bs {
        constructor() {
          super();
          $(this, Pn);
          $(this, Jr);
          $(this, Ps);
          I(this, Ps, (t) => {
            if (typeof window < "u" && window.addEventListener) {
              const r = () => t();
              return (
                window.addEventListener("visibilitychange", r, !1),
                () => {
                  window.removeEventListener("visibilitychange", r);
                }
              );
            }
          });
        }
        onSubscribe() {
          h(this, Jr) || this.setEventListener(h(this, Ps));
        }
        onUnsubscribe() {
          var t;
          this.hasListeners() ||
            ((t = h(this, Jr)) == null || t.call(this), I(this, Jr, void 0));
        }
        setEventListener(t) {
          var r;
          (I(this, Ps, t),
            (r = h(this, Jr)) == null || r.call(this),
            I(
              this,
              Jr,
              t((n) => {
                typeof n == "boolean" ? this.setFocused(n) : this.onFocus();
              })
            ));
        }
        setFocused(t) {
          h(this, Pn) !== t && (I(this, Pn, t), this.onFocus());
        }
        onFocus() {
          const t = this.isFocused();
          this.listeners.forEach((r) => {
            r(t);
          });
        }
        isFocused() {
          return typeof h(this, Pn) == "boolean"
            ? h(this, Pn)
            : globalThis.document?.visibilityState !== "hidden";
        }
      }),
      (Pn = new WeakMap()),
      (Jr = new WeakMap()),
      (Ps = new WeakMap()),
      cy),
    ou = new Gv(),
    Yv = {
      setTimeout: (e, t) => setTimeout(e, t),
      clearTimeout: (e) => clearTimeout(e),
      setInterval: (e, t) => setInterval(e, t),
      clearInterval: (e) => clearInterval(e),
    },
    Jv =
      ((uy = class {
        constructor() {
          $(this, Xr, Yv);
          $(this, ad, !1);
        }
        setTimeoutProvider(e) {
          I(this, Xr, e);
        }
        setTimeout(e, t) {
          return h(this, Xr).setTimeout(e, t);
        }
        clearTimeout(e) {
          h(this, Xr).clearTimeout(e);
        }
        setInterval(e, t) {
          return h(this, Xr).setInterval(e, t);
        }
        clearInterval(e) {
          h(this, Xr).clearInterval(e);
        }
      }),
      (Xr = new WeakMap()),
      (ad = new WeakMap()),
      uy),
    Sn = new Jv();
  function Xv(e) {
    setTimeout(e, 0);
  }
  var ex = typeof window > "u" || "Deno" in globalThis;
  function st() {}
  function tx(e, t) {
    return typeof e == "function" ? e(t) : e;
  }
  function iu(e) {
    return typeof e == "number" && e >= 0 && e !== 1 / 0;
  }
  function Zf(e, t) {
    return Math.max(e + (t || 0) - Date.now(), 0);
  }
  function Vr(e, t) {
    return typeof e == "function" ? e(t) : e;
  }
  function Tt(e, t) {
    return typeof e == "function" ? e(t) : e;
  }
  function Bf(e, t) {
    const {
      type: r = "all",
      exact: n,
      fetchStatus: s,
      predicate: a,
      queryKey: o,
      stale: l,
    } = e;
    if (o) {
      if (n) {
        if (t.queryHash !== lu(o, t.options)) return !1;
      } else if (!Ia(t.queryKey, o)) return !1;
    }
    if (r !== "all") {
      const c = t.isActive();
      if ((r === "active" && !c) || (r === "inactive" && c)) return !1;
    }
    return !(
      (typeof l == "boolean" && t.isStale() !== l) ||
      (s && s !== t.state.fetchStatus) ||
      (a && !a(t))
    );
  }
  function qf(e, t) {
    const { exact: r, status: n, predicate: s, mutationKey: a } = e;
    if (a) {
      if (!t.options.mutationKey) return !1;
      if (r) {
        if (Cn(t.options.mutationKey) !== Cn(a)) return !1;
      } else if (!Ia(t.options.mutationKey, a)) return !1;
    }
    return !((n && t.state.status !== n) || (s && !s(t)));
  }
  function lu(e, t) {
    return (t?.queryKeyHashFn || Cn)(e);
  }
  function Cn(e) {
    return JSON.stringify(e, (t, r) =>
      cu(r)
        ? Object.keys(r)
            .sort()
            .reduce((n, s) => ((n[s] = r[s]), n), {})
        : r
    );
  }
  function Ia(e, t) {
    return e === t
      ? !0
      : typeof e != typeof t
        ? !1
        : e && t && typeof e == "object" && typeof t == "object"
          ? Object.keys(t).every((r) => Ia(e[r], t[r]))
          : !1;
  }
  var rx = Object.prototype.hasOwnProperty;
  function Vf(e, t, r = 0) {
    if (e === t) return e;
    if (r > 500) return t;
    const n = Kf(e) && Kf(t);
    if (!n && !(cu(e) && cu(t))) return t;
    const a = (n ? e : Object.keys(e)).length,
      o = n ? t : Object.keys(t),
      l = o.length,
      c = n ? new Array(l) : {};
    let u = 0;
    for (let d = 0; d < l; d++) {
      const f = n ? d : o[d],
        m = e[f],
        v = t[f];
      if (m === v) {
        ((c[f] = m), (n ? d < a : rx.call(e, f)) && u++);
        continue;
      }
      if (
        m === null ||
        v === null ||
        typeof m != "object" ||
        typeof v != "object"
      ) {
        c[f] = v;
        continue;
      }
      const x = Vf(m, v, r + 1);
      ((c[f] = x), x === m && u++);
    }
    return a === l && u === a ? e : c;
  }
  function gi(e, t) {
    if (!t || Object.keys(e).length !== Object.keys(t).length) return !1;
    for (const r in e) if (e[r] !== t[r]) return !1;
    return !0;
  }
  function Kf(e) {
    return Array.isArray(e) && e.length === Object.keys(e).length;
  }
  function cu(e) {
    if (!Wf(e)) return !1;
    const t = e.constructor;
    if (t === void 0) return !0;
    const r = t.prototype;
    return !(
      !Wf(r) ||
      !r.hasOwnProperty("isPrototypeOf") ||
      Object.getPrototypeOf(e) !== Object.prototype
    );
  }
  function Wf(e) {
    return Object.prototype.toString.call(e) === "[object Object]";
  }
  function nx(e) {
    return new Promise((t) => {
      Sn.setTimeout(t, e);
    });
  }
  function uu(e, t, r) {
    return typeof r.structuralSharing == "function"
      ? r.structuralSharing(e, t)
      : r.structuralSharing !== !1
        ? Vf(e, t)
        : t;
  }
  function sx(e, t, r = 0) {
    const n = [...e, t];
    return r && n.length > r ? n.slice(1) : n;
  }
  function ax(e, t, r = 0) {
    const n = [t, ...e];
    return r && n.length > r ? n.slice(0, -1) : n;
  }
  var du = Symbol();
  function Hf(e, t) {
    return !e.queryFn && t?.initialPromise
      ? () => t.initialPromise
      : !e.queryFn || e.queryFn === du
        ? () => Promise.reject(new Error(`Missing queryFn: '${e.queryHash}'`))
        : e.queryFn;
  }
  function pu(e, t) {
    return typeof e == "function" ? e(...t) : !!e;
  }
  function ox(e, t, r) {
    let n = !1,
      s;
    return (
      Object.defineProperty(e, "signal", {
        enumerable: !0,
        get: () => (
          s ?? (s = t()),
          n ||
            ((n = !0),
            s.aborted ? r() : s.addEventListener("abort", r, { once: !0 })),
          s
        ),
      }),
      e
    );
  }
  var La = (() => {
    let e = () => ex;
    return {
      isServer() {
        return e();
      },
      setIsServer(t) {
        e = t;
      },
    };
  })();
  function mu() {
    let e, t;
    const r = new Promise((s, a) => {
      ((e = s), (t = a));
    });
    ((r.status = "pending"), r.catch(() => {}));
    function n(s) {
      (Object.assign(r, s), delete r.resolve, delete r.reject);
    }
    return (
      (r.resolve = (s) => {
        (n({ status: "fulfilled", value: s }), e(s));
      }),
      (r.reject = (s) => {
        (n({ status: "rejected", reason: s }), t(s));
      }),
      r
    );
  }
  var ix = Xv;
  function lx() {
    let e = [],
      t = 0,
      r = (l) => {
        l();
      },
      n = (l) => {
        l();
      },
      s = ix;
    const a = (l) => {
        t
          ? e.push(l)
          : s(() => {
              r(l);
            });
      },
      o = () => {
        const l = e;
        ((e = []),
          l.length &&
            s(() => {
              n(() => {
                l.forEach((c) => {
                  r(c);
                });
              });
            }));
      };
    return {
      batch: (l) => {
        let c;
        t++;
        try {
          c = l();
        } finally {
          (t--, t || o());
        }
        return c;
      },
      batchCalls:
        (l) =>
        (...c) => {
          a(() => {
            l(...c);
          });
        },
      schedule: a,
      setNotifyFunction: (l) => {
        r = l;
      },
      setBatchNotifyFunction: (l) => {
        n = l;
      },
      setScheduler: (l) => {
        s = l;
      },
    };
  }
  var $e = lx(),
    cx =
      ((dy = class extends bs {
        constructor() {
          super();
          $(this, Os, !0);
          $(this, en);
          $(this, Is);
          I(this, Is, (t) => {
            if (typeof window < "u" && window.addEventListener) {
              const r = () => t(!0),
                n = () => t(!1);
              return (
                window.addEventListener("online", r, !1),
                window.addEventListener("offline", n, !1),
                () => {
                  (window.removeEventListener("online", r),
                    window.removeEventListener("offline", n));
                }
              );
            }
          });
        }
        onSubscribe() {
          h(this, en) || this.setEventListener(h(this, Is));
        }
        onUnsubscribe() {
          var t;
          this.hasListeners() ||
            ((t = h(this, en)) == null || t.call(this), I(this, en, void 0));
        }
        setEventListener(t) {
          var r;
          (I(this, Is, t),
            (r = h(this, en)) == null || r.call(this),
            I(this, en, t(this.setOnline.bind(this))));
        }
        setOnline(t) {
          h(this, Os) !== t &&
            (I(this, Os, t),
            this.listeners.forEach((n) => {
              n(t);
            }));
        }
        isOnline() {
          return h(this, Os);
        }
      }),
      (Os = new WeakMap()),
      (en = new WeakMap()),
      (Is = new WeakMap()),
      dy),
    yi = new cx();
  function ux(e) {
    return Math.min(1e3 * 2 ** e, 3e4);
  }
  function Qf(e) {
    return (e ?? "online") === "online" ? yi.isOnline() : !0;
  }
  var fu = class extends Error {
    constructor(e) {
      (super("CancelledError"),
        (this.revert = e?.revert),
        (this.silent = e?.silent));
    }
  };
  function Gf(e) {
    let t = !1,
      r = 0,
      n;
    const s = mu(),
      a = () => s.status !== "pending",
      o = (b) => {
        if (!a()) {
          const j = new fu(b);
          (m(j), e.onCancel?.(j));
        }
      },
      l = () => {
        t = !0;
      },
      c = () => {
        t = !1;
      },
      u = () =>
        ou.isFocused() &&
        (e.networkMode === "always" || yi.isOnline()) &&
        e.canRun(),
      d = () => Qf(e.networkMode) && e.canRun(),
      f = (b) => {
        a() || (n?.(), s.resolve(b));
      },
      m = (b) => {
        a() || (n?.(), s.reject(b));
      },
      v = () =>
        new Promise((b) => {
          ((n = (j) => {
            (a() || u()) && b(j);
          }),
            e.onPause?.());
        }).then(() => {
          ((n = void 0), a() || e.onContinue?.());
        }),
      x = () => {
        if (a()) return;
        let b;
        const j = r === 0 ? e.initialPromise : void 0;
        try {
          b = j ?? e.fn();
        } catch (g) {
          b = Promise.reject(g);
        }
        Promise.resolve(b)
          .then(f)
          .catch((g) => {
            if (a()) return;
            const p = e.retry ?? (La.isServer() ? 0 : 3),
              y = e.retryDelay ?? ux,
              w = typeof y == "function" ? y(r, g) : y,
              S =
                p === !0 ||
                (typeof p == "number" && r < p) ||
                (typeof p == "function" && p(r, g));
            if (t || !S) {
              m(g);
              return;
            }
            (r++,
              e.onFail?.(r, g),
              nx(w)
                .then(() => (u() ? void 0 : v()))
                .then(() => {
                  t ? m(g) : x();
                }));
          });
      };
    return {
      promise: s,
      status: () => s.status,
      cancel: o,
      continue: () => (n?.(), s),
      cancelRetry: l,
      continueRetry: c,
      canStart: d,
      start: () => (d() ? x() : v().then(x), s),
    };
  }
  var Yf =
      ((py = class {
        constructor() {
          $(this, On);
        }
        destroy() {
          this.clearGcTimeout();
        }
        scheduleGc() {
          (this.clearGcTimeout(),
            iu(this.gcTime) &&
              I(
                this,
                On,
                Sn.setTimeout(() => {
                  this.optionalRemove();
                }, this.gcTime)
              ));
        }
        updateGcTime(e) {
          this.gcTime = Math.max(
            this.gcTime || 0,
            e ?? (La.isServer() ? 1 / 0 : 300 * 1e3)
          );
        }
        clearGcTimeout() {
          h(this, On) && (Sn.clearTimeout(h(this, On)), I(this, On, void 0));
        }
      }),
      (On = new WeakMap()),
      py),
    dx =
      ((my = class extends Yf {
        constructor(t) {
          super();
          $(this, St);
          $(this, In);
          $(this, Ls);
          $(this, Lt);
          $(this, Ln);
          $(this, Be);
          $(this, qa);
          $(this, Dn);
          (I(this, Dn, !1),
            I(this, qa, t.defaultOptions),
            this.setOptions(t.options),
            (this.observers = []),
            I(this, Ln, t.client),
            I(this, Lt, h(this, Ln).getQueryCache()),
            (this.queryKey = t.queryKey),
            (this.queryHash = t.queryHash),
            I(this, In, eh(this.options)),
            (this.state = t.state ?? h(this, In)),
            this.scheduleGc());
        }
        get meta() {
          return this.options.meta;
        }
        get promise() {
          return h(this, Be)?.promise;
        }
        setOptions(t) {
          if (
            ((this.options = { ...h(this, qa), ...t }),
            this.updateGcTime(this.options.gcTime),
            this.state && this.state.data === void 0)
          ) {
            const r = eh(this.options);
            r.data !== void 0 &&
              (this.setState(Xf(r.data, r.dataUpdatedAt)), I(this, In, r));
          }
        }
        optionalRemove() {
          !this.observers.length &&
            this.state.fetchStatus === "idle" &&
            h(this, Lt).remove(this);
        }
        setData(t, r) {
          const n = uu(this.state.data, t, this.options);
          return (
            G(this, St, Sr).call(this, {
              data: n,
              type: "success",
              dataUpdatedAt: r?.updatedAt,
              manual: r?.manual,
            }),
            n
          );
        }
        setState(t, r) {
          G(this, St, Sr).call(this, {
            type: "setState",
            state: t,
            setStateOptions: r,
          });
        }
        cancel(t) {
          const r = h(this, Be)?.promise;
          return (
            h(this, Be)?.cancel(t),
            r ? r.then(st).catch(st) : Promise.resolve()
          );
        }
        destroy() {
          (super.destroy(), this.cancel({ silent: !0 }));
        }
        get resetState() {
          return h(this, In);
        }
        reset() {
          (this.destroy(), this.setState(this.resetState));
        }
        isActive() {
          return this.observers.some((t) => Tt(t.options.enabled, this) !== !1);
        }
        isDisabled() {
          return this.getObserversCount() > 0
            ? !this.isActive()
            : this.options.queryFn === du || !this.isFetched();
        }
        isFetched() {
          return this.state.dataUpdateCount + this.state.errorUpdateCount > 0;
        }
        isStatic() {
          return this.getObserversCount() > 0
            ? this.observers.some(
                (t) => Vr(t.options.staleTime, this) === "static"
              )
            : !1;
        }
        isStale() {
          return this.getObserversCount() > 0
            ? this.observers.some((t) => t.getCurrentResult().isStale)
            : this.state.data === void 0 || this.state.isInvalidated;
        }
        isStaleByTime(t = 0) {
          return this.state.data === void 0
            ? !0
            : t === "static"
              ? !1
              : this.state.isInvalidated
                ? !0
                : !Zf(this.state.dataUpdatedAt, t);
        }
        onFocus() {
          (this.observers
            .find((r) => r.shouldFetchOnWindowFocus())
            ?.refetch({ cancelRefetch: !1 }),
            h(this, Be)?.continue());
        }
        onOnline() {
          (this.observers
            .find((r) => r.shouldFetchOnReconnect())
            ?.refetch({ cancelRefetch: !1 }),
            h(this, Be)?.continue());
        }
        addObserver(t) {
          this.observers.includes(t) ||
            (this.observers.push(t),
            this.clearGcTimeout(),
            h(this, Lt).notify({
              type: "observerAdded",
              query: this,
              observer: t,
            }));
        }
        removeObserver(t) {
          this.observers.includes(t) &&
            ((this.observers = this.observers.filter((r) => r !== t)),
            this.observers.length ||
              (h(this, Be) &&
                (h(this, Dn) || G(this, St, wy).call(this)
                  ? h(this, Be).cancel({ revert: !0 })
                  : h(this, Be).cancelRetry()),
              this.scheduleGc()),
            h(this, Lt).notify({
              type: "observerRemoved",
              query: this,
              observer: t,
            }));
        }
        getObserversCount() {
          return this.observers.length;
        }
        invalidate() {
          this.state.isInvalidated ||
            G(this, St, Sr).call(this, { type: "invalidate" });
        }
        async fetch(t, r) {
          if (
            this.state.fetchStatus !== "idle" &&
            h(this, Be)?.status() !== "rejected"
          ) {
            if (this.state.data !== void 0 && r?.cancelRefetch)
              this.cancel({ silent: !0 });
            else if (h(this, Be))
              return (h(this, Be).continueRetry(), h(this, Be).promise);
          }
          if ((t && this.setOptions(t), !this.options.queryFn)) {
            const c = this.observers.find((u) => u.options.queryFn);
            c && this.setOptions(c.options);
          }
          const n = new AbortController(),
            s = (c) => {
              Object.defineProperty(c, "signal", {
                enumerable: !0,
                get: () => (I(this, Dn, !0), n.signal),
              });
            },
            a = () => {
              const c = Hf(this.options, r),
                d = (() => {
                  const f = {
                    client: h(this, Ln),
                    queryKey: this.queryKey,
                    meta: this.meta,
                  };
                  return (s(f), f);
                })();
              return (
                I(this, Dn, !1),
                this.options.persister
                  ? this.options.persister(c, d, this)
                  : c(d)
              );
            },
            l = (() => {
              const c = {
                fetchOptions: r,
                options: this.options,
                queryKey: this.queryKey,
                client: h(this, Ln),
                state: this.state,
                fetchFn: a,
              };
              return (s(c), c);
            })();
          (this.options.behavior?.onFetch(l, this),
            I(this, Ls, this.state),
            (this.state.fetchStatus === "idle" ||
              this.state.fetchMeta !== l.fetchOptions?.meta) &&
              G(this, St, Sr).call(this, {
                type: "fetch",
                meta: l.fetchOptions?.meta,
              }),
            I(
              this,
              Be,
              Gf({
                initialPromise: r?.initialPromise,
                fn: l.fetchFn,
                onCancel: (c) => {
                  (c instanceof fu &&
                    c.revert &&
                    this.setState({ ...h(this, Ls), fetchStatus: "idle" }),
                    n.abort());
                },
                onFail: (c, u) => {
                  G(this, St, Sr).call(this, {
                    type: "failed",
                    failureCount: c,
                    error: u,
                  });
                },
                onPause: () => {
                  G(this, St, Sr).call(this, { type: "pause" });
                },
                onContinue: () => {
                  G(this, St, Sr).call(this, { type: "continue" });
                },
                retry: l.options.retry,
                retryDelay: l.options.retryDelay,
                networkMode: l.options.networkMode,
                canRun: () => !0,
              })
            ));
          try {
            const c = await h(this, Be).start();
            if (c === void 0)
              throw new Error(`${this.queryHash} data is undefined`);
            return (
              this.setData(c),
              h(this, Lt).config.onSuccess?.(c, this),
              h(this, Lt).config.onSettled?.(c, this.state.error, this),
              c
            );
          } catch (c) {
            if (c instanceof fu) {
              if (c.silent) return h(this, Be).promise;
              if (c.revert) {
                if (this.state.data === void 0) throw c;
                return this.state.data;
              }
            }
            throw (
              G(this, St, Sr).call(this, { type: "error", error: c }),
              h(this, Lt).config.onError?.(c, this),
              h(this, Lt).config.onSettled?.(this.state.data, c, this),
              c
            );
          } finally {
            this.scheduleGc();
          }
        }
      }),
      (In = new WeakMap()),
      (Ls = new WeakMap()),
      (Lt = new WeakMap()),
      (Ln = new WeakMap()),
      (Be = new WeakMap()),
      (qa = new WeakMap()),
      (Dn = new WeakMap()),
      (St = new WeakSet()),
      (wy = function () {
        return (
          this.state.fetchStatus === "paused" && this.state.status === "pending"
        );
      }),
      (Sr = function (t) {
        const r = (n) => {
          switch (t.type) {
            case "failed":
              return {
                ...n,
                fetchFailureCount: t.failureCount,
                fetchFailureReason: t.error,
              };
            case "pause":
              return { ...n, fetchStatus: "paused" };
            case "continue":
              return { ...n, fetchStatus: "fetching" };
            case "fetch":
              return {
                ...n,
                ...Jf(n.data, this.options),
                fetchMeta: t.meta ?? null,
              };
            case "success":
              const s = {
                ...n,
                ...Xf(t.data, t.dataUpdatedAt),
                dataUpdateCount: n.dataUpdateCount + 1,
                ...(!t.manual && {
                  fetchStatus: "idle",
                  fetchFailureCount: 0,
                  fetchFailureReason: null,
                }),
              };
              return (I(this, Ls, t.manual ? s : void 0), s);
            case "error":
              const a = t.error;
              return {
                ...n,
                error: a,
                errorUpdateCount: n.errorUpdateCount + 1,
                errorUpdatedAt: Date.now(),
                fetchFailureCount: n.fetchFailureCount + 1,
                fetchFailureReason: a,
                fetchStatus: "idle",
                status: "error",
                isInvalidated: !0,
              };
            case "invalidate":
              return { ...n, isInvalidated: !0 };
            case "setState":
              return { ...n, ...t.state };
          }
        };
        ((this.state = r(this.state)),
          $e.batch(() => {
            (this.observers.forEach((n) => {
              n.onQueryUpdate();
            }),
              h(this, Lt).notify({ query: this, type: "updated", action: t }));
          }));
      }),
      my);
  function Jf(e, t) {
    return {
      fetchFailureCount: 0,
      fetchFailureReason: null,
      fetchStatus: Qf(t.networkMode) ? "fetching" : "paused",
      ...(e === void 0 && { error: null, status: "pending" }),
    };
  }
  function Xf(e, t) {
    return {
      data: e,
      dataUpdatedAt: t ?? Date.now(),
      error: null,
      isInvalidated: !1,
      status: "success",
    };
  }
  function eh(e) {
    const t =
        typeof e.initialData == "function" ? e.initialData() : e.initialData,
      r = t !== void 0,
      n = r
        ? typeof e.initialDataUpdatedAt == "function"
          ? e.initialDataUpdatedAt()
          : e.initialDataUpdatedAt
        : 0;
    return {
      data: t,
      dataUpdateCount: 0,
      dataUpdatedAt: r ? (n ?? Date.now()) : 0,
      error: null,
      errorUpdateCount: 0,
      errorUpdatedAt: 0,
      fetchFailureCount: 0,
      fetchFailureReason: null,
      fetchMeta: null,
      isInvalidated: !1,
      status: r ? "success" : "pending",
      fetchStatus: "idle",
    };
  }
  var px =
    ((fy = class extends bs {
      constructor(t, r) {
        super();
        $(this, ie);
        $(this, ft);
        $(this, re);
        $(this, Va);
        $(this, it);
        $(this, zn);
        $(this, Ds);
        $(this, xr);
        $(this, tn);
        $(this, Ka);
        $(this, zs);
        $(this, Ms);
        $(this, Mn);
        $(this, $n);
        $(this, rn);
        $(this, $s, new Set());
        ((this.options = r),
          I(this, ft, t),
          I(this, tn, null),
          I(this, xr, mu()),
          this.bindMethods(),
          this.setOptions(r));
      }
      bindMethods() {
        this.refetch = this.refetch.bind(this);
      }
      onSubscribe() {
        this.listeners.size === 1 &&
          (h(this, re).addObserver(this),
          th(h(this, re), this.options)
            ? G(this, ie, Ja).call(this)
            : this.updateResult(),
          G(this, ie, dd).call(this));
      }
      onUnsubscribe() {
        this.hasListeners() || this.destroy();
      }
      shouldFetchOnReconnect() {
        return hu(h(this, re), this.options, this.options.refetchOnReconnect);
      }
      shouldFetchOnWindowFocus() {
        return hu(h(this, re), this.options, this.options.refetchOnWindowFocus);
      }
      destroy() {
        ((this.listeners = new Set()),
          G(this, ie, pd).call(this),
          G(this, ie, md).call(this),
          h(this, re).removeObserver(this));
      }
      setOptions(t) {
        const r = this.options,
          n = h(this, re);
        if (
          ((this.options = h(this, ft).defaultQueryOptions(t)),
          this.options.enabled !== void 0 &&
            typeof this.options.enabled != "boolean" &&
            typeof this.options.enabled != "function" &&
            typeof Tt(this.options.enabled, h(this, re)) != "boolean")
        )
          throw new Error(
            "Expected enabled to be a boolean or a callback that returns a boolean"
          );
        (G(this, ie, fd).call(this),
          h(this, re).setOptions(this.options),
          r._defaulted &&
            !gi(this.options, r) &&
            h(this, ft)
              .getQueryCache()
              .notify({
                type: "observerOptionsUpdated",
                query: h(this, re),
                observer: this,
              }));
        const s = this.hasListeners();
        (s && rh(h(this, re), n, this.options, r) && G(this, ie, Ja).call(this),
          this.updateResult(),
          s &&
            (h(this, re) !== n ||
              Tt(this.options.enabled, h(this, re)) !==
                Tt(r.enabled, h(this, re)) ||
              Vr(this.options.staleTime, h(this, re)) !==
                Vr(r.staleTime, h(this, re))) &&
            G(this, ie, ld).call(this));
        const a = G(this, ie, cd).call(this);
        s &&
          (h(this, re) !== n ||
            Tt(this.options.enabled, h(this, re)) !==
              Tt(r.enabled, h(this, re)) ||
            a !== h(this, rn)) &&
          G(this, ie, ud).call(this, a);
      }
      getOptimisticResult(t) {
        const r = h(this, ft).getQueryCache().build(h(this, ft), t),
          n = this.createResult(r, t);
        return (
          fx(this, n) &&
            (I(this, it, n),
            I(this, Ds, this.options),
            I(this, zn, h(this, re).state)),
          n
        );
      }
      getCurrentResult() {
        return h(this, it);
      }
      trackResult(t, r) {
        return new Proxy(t, {
          get: (n, s) => (
            this.trackProp(s),
            r?.(s),
            s === "promise" &&
              (this.trackProp("data"),
              !this.options.experimental_prefetchInRender &&
                h(this, xr).status === "pending" &&
                h(this, xr).reject(
                  new Error(
                    "experimental_prefetchInRender feature flag is not enabled"
                  )
                )),
            Reflect.get(n, s)
          ),
        });
      }
      trackProp(t) {
        h(this, $s).add(t);
      }
      getCurrentQuery() {
        return h(this, re);
      }
      refetch({ ...t } = {}) {
        return this.fetch({ ...t });
      }
      fetchOptimistic(t) {
        const r = h(this, ft).defaultQueryOptions(t),
          n = h(this, ft).getQueryCache().build(h(this, ft), r);
        return n.fetch().then(() => this.createResult(n, r));
      }
      fetch(t) {
        return G(this, ie, Ja)
          .call(this, { ...t, cancelRefetch: t.cancelRefetch ?? !0 })
          .then(() => (this.updateResult(), h(this, it)));
      }
      createResult(t, r) {
        const n = h(this, re),
          s = this.options,
          a = h(this, it),
          o = h(this, zn),
          l = h(this, Ds),
          u = t !== n ? t.state : h(this, Va),
          { state: d } = t;
        let f = { ...d },
          m = !1,
          v;
        if (r._optimisticResults) {
          const _ = this.hasListeners(),
            O = !_ && th(t, r),
            q = _ && rh(t, n, r, s);
          ((O || q) && (f = { ...f, ...Jf(d.data, t.options) }),
            r._optimisticResults === "isRestoring" && (f.fetchStatus = "idle"));
        }
        let { error: x, errorUpdatedAt: b, status: j } = f;
        v = f.data;
        let g = !1;
        if (r.placeholderData !== void 0 && v === void 0 && j === "pending") {
          let _;
          (a?.isPlaceholderData && r.placeholderData === l?.placeholderData
            ? ((_ = a.data), (g = !0))
            : (_ =
                typeof r.placeholderData == "function"
                  ? r.placeholderData(h(this, Ms)?.state.data, h(this, Ms))
                  : r.placeholderData),
            _ !== void 0 &&
              ((j = "success"), (v = uu(a?.data, _, r)), (m = !0)));
        }
        if (r.select && v !== void 0 && !g)
          if (a && v === o?.data && r.select === h(this, Ka)) v = h(this, zs);
          else
            try {
              (I(this, Ka, r.select),
                (v = r.select(v)),
                (v = uu(a?.data, v, r)),
                I(this, zs, v),
                I(this, tn, null));
            } catch (_) {
              I(this, tn, _);
            }
        h(this, tn) &&
          ((x = h(this, tn)),
          (v = h(this, zs)),
          (b = Date.now()),
          (j = "error"));
        const p = f.fetchStatus === "fetching",
          y = j === "pending",
          w = j === "error",
          S = y && p,
          A = v !== void 0,
          T = {
            status: j,
            fetchStatus: f.fetchStatus,
            isPending: y,
            isSuccess: j === "success",
            isError: w,
            isInitialLoading: S,
            isLoading: S,
            data: v,
            dataUpdatedAt: f.dataUpdatedAt,
            error: x,
            errorUpdatedAt: b,
            failureCount: f.fetchFailureCount,
            failureReason: f.fetchFailureReason,
            errorUpdateCount: f.errorUpdateCount,
            isFetched: t.isFetched(),
            isFetchedAfterMount:
              f.dataUpdateCount > u.dataUpdateCount ||
              f.errorUpdateCount > u.errorUpdateCount,
            isFetching: p,
            isRefetching: p && !y,
            isLoadingError: w && !A,
            isPaused: f.fetchStatus === "paused",
            isPlaceholderData: m,
            isRefetchError: w && A,
            isStale: gu(t, r),
            refetch: this.refetch,
            promise: h(this, xr),
            isEnabled: Tt(r.enabled, t) !== !1,
          };
        if (this.options.experimental_prefetchInRender) {
          const _ = T.data !== void 0,
            O = T.status === "error" && !_,
            q = (Oe) => {
              O ? Oe.reject(T.error) : _ && Oe.resolve(T.data);
            },
            ee = () => {
              const Oe = I(this, xr, (T.promise = mu()));
              q(Oe);
            },
            Z = h(this, xr);
          switch (Z.status) {
            case "pending":
              t.queryHash === n.queryHash && q(Z);
              break;
            case "fulfilled":
              (O || T.data !== Z.value) && ee();
              break;
            case "rejected":
              (!O || T.error !== Z.reason) && ee();
              break;
          }
        }
        return T;
      }
      updateResult() {
        const t = h(this, it),
          r = this.createResult(h(this, re), this.options);
        if (
          (I(this, zn, h(this, re).state),
          I(this, Ds, this.options),
          h(this, zn).data !== void 0 && I(this, Ms, h(this, re)),
          gi(r, t))
        )
          return;
        I(this, it, r);
        const n = () => {
          if (!t) return !0;
          const { notifyOnChangeProps: s } = this.options,
            a = typeof s == "function" ? s() : s;
          if (a === "all" || (!a && !h(this, $s).size)) return !0;
          const o = new Set(a ?? h(this, $s));
          return (
            this.options.throwOnError && o.add("error"),
            Object.keys(h(this, it)).some((l) => {
              const c = l;
              return h(this, it)[c] !== t[c] && o.has(c);
            })
          );
        };
        G(this, ie, ky).call(this, { listeners: n() });
      }
      onQueryUpdate() {
        (this.updateResult(),
          this.hasListeners() && G(this, ie, dd).call(this));
      }
    }),
    (ft = new WeakMap()),
    (re = new WeakMap()),
    (Va = new WeakMap()),
    (it = new WeakMap()),
    (zn = new WeakMap()),
    (Ds = new WeakMap()),
    (xr = new WeakMap()),
    (tn = new WeakMap()),
    (Ka = new WeakMap()),
    (zs = new WeakMap()),
    (Ms = new WeakMap()),
    (Mn = new WeakMap()),
    ($n = new WeakMap()),
    (rn = new WeakMap()),
    ($s = new WeakMap()),
    (ie = new WeakSet()),
    (Ja = function (t) {
      G(this, ie, fd).call(this);
      let r = h(this, re).fetch(this.options, t);
      return (t?.throwOnError || (r = r.catch(st)), r);
    }),
    (ld = function () {
      G(this, ie, pd).call(this);
      const t = Vr(this.options.staleTime, h(this, re));
      if (La.isServer() || h(this, it).isStale || !iu(t)) return;
      const n = Zf(h(this, it).dataUpdatedAt, t) + 1;
      I(
        this,
        Mn,
        Sn.setTimeout(() => {
          h(this, it).isStale || this.updateResult();
        }, n)
      );
    }),
    (cd = function () {
      return (
        (typeof this.options.refetchInterval == "function"
          ? this.options.refetchInterval(h(this, re))
          : this.options.refetchInterval) ?? !1
      );
    }),
    (ud = function (t) {
      (G(this, ie, md).call(this),
        I(this, rn, t),
        !(
          La.isServer() ||
          Tt(this.options.enabled, h(this, re)) === !1 ||
          !iu(h(this, rn)) ||
          h(this, rn) === 0
        ) &&
          I(
            this,
            $n,
            Sn.setInterval(
              () => {
                (this.options.refetchIntervalInBackground || ou.isFocused()) &&
                  G(this, ie, Ja).call(this);
              },
              h(this, rn)
            )
          ));
    }),
    (dd = function () {
      (G(this, ie, ld).call(this),
        G(this, ie, ud).call(this, G(this, ie, cd).call(this)));
    }),
    (pd = function () {
      h(this, Mn) && (Sn.clearTimeout(h(this, Mn)), I(this, Mn, void 0));
    }),
    (md = function () {
      h(this, $n) && (Sn.clearInterval(h(this, $n)), I(this, $n, void 0));
    }),
    (fd = function () {
      const t = h(this, ft).getQueryCache().build(h(this, ft), this.options);
      if (t === h(this, re)) return;
      const r = h(this, re);
      (I(this, re, t),
        I(this, Va, t.state),
        this.hasListeners() && (r?.removeObserver(this), t.addObserver(this)));
    }),
    (ky = function (t) {
      $e.batch(() => {
        (t.listeners &&
          this.listeners.forEach((r) => {
            r(h(this, it));
          }),
          h(this, ft)
            .getQueryCache()
            .notify({ query: h(this, re), type: "observerResultsUpdated" }));
      });
    }),
    fy);
  function mx(e, t) {
    return (
      Tt(t.enabled, e) !== !1 &&
      e.state.data === void 0 &&
      !(e.state.status === "error" && t.retryOnMount === !1)
    );
  }
  function th(e, t) {
    return mx(e, t) || (e.state.data !== void 0 && hu(e, t, t.refetchOnMount));
  }
  function hu(e, t, r) {
    if (Tt(t.enabled, e) !== !1 && Vr(t.staleTime, e) !== "static") {
      const n = typeof r == "function" ? r(e) : r;
      return n === "always" || (n !== !1 && gu(e, t));
    }
    return !1;
  }
  function rh(e, t, r, n) {
    return (
      (e !== t || Tt(n.enabled, e) === !1) &&
      (!r.suspense || e.state.status !== "error") &&
      gu(e, r)
    );
  }
  function gu(e, t) {
    return Tt(t.enabled, e) !== !1 && e.isStaleByTime(Vr(t.staleTime, e));
  }
  function fx(e, t) {
    return !gi(e.getCurrentResult(), t);
  }
  function nh(e) {
    return {
      onFetch: (t, r) => {
        const n = t.options,
          s = t.fetchOptions?.meta?.fetchMore?.direction,
          a = t.state.data?.pages || [],
          o = t.state.data?.pageParams || [];
        let l = { pages: [], pageParams: [] },
          c = 0;
        const u = async () => {
          let d = !1;
          const f = (x) => {
              ox(
                x,
                () => t.signal,
                () => (d = !0)
              );
            },
            m = Hf(t.options, t.fetchOptions),
            v = async (x, b, j) => {
              if (d) return Promise.reject();
              if (b == null && x.pages.length) return Promise.resolve(x);
              const p = (() => {
                  const A = {
                    client: t.client,
                    queryKey: t.queryKey,
                    pageParam: b,
                    direction: j ? "backward" : "forward",
                    meta: t.options.meta,
                  };
                  return (f(A), A);
                })(),
                y = await m(p),
                { maxPages: w } = t.options,
                S = j ? ax : sx;
              return {
                pages: S(x.pages, y, w),
                pageParams: S(x.pageParams, b, w),
              };
            };
          if (s && a.length) {
            const x = s === "backward",
              b = x ? hx : sh,
              j = { pages: a, pageParams: o },
              g = b(n, j);
            l = await v(j, g, x);
          } else {
            const x = e ?? a.length;
            do {
              const b = c === 0 ? (o[0] ?? n.initialPageParam) : sh(n, l);
              if (c > 0 && b == null) break;
              ((l = await v(l, b)), c++);
            } while (c < x);
          }
          return l;
        };
        t.options.persister
          ? (t.fetchFn = () =>
              t.options.persister?.(
                u,
                {
                  client: t.client,
                  queryKey: t.queryKey,
                  meta: t.options.meta,
                  signal: t.signal,
                },
                r
              ))
          : (t.fetchFn = u);
      },
    };
  }
  function sh(e, { pages: t, pageParams: r }) {
    const n = t.length - 1;
    return t.length > 0 ? e.getNextPageParam(t[n], t, r[n], r) : void 0;
  }
  function hx(e, { pages: t, pageParams: r }) {
    return t.length > 0 ? e.getPreviousPageParam?.(t[0], t, r[0], r) : void 0;
  }
  var gx =
    ((hy = class extends Yf {
      constructor(t) {
        super();
        $(this, tr);
        $(this, Wa);
        $(this, er);
        $(this, Xe);
        $(this, Fn);
        (I(this, Wa, t.client),
          (this.mutationId = t.mutationId),
          I(this, Xe, t.mutationCache),
          I(this, er, []),
          (this.state = t.state || ah()),
          this.setOptions(t.options),
          this.scheduleGc());
      }
      setOptions(t) {
        ((this.options = t), this.updateGcTime(this.options.gcTime));
      }
      get meta() {
        return this.options.meta;
      }
      addObserver(t) {
        h(this, er).includes(t) ||
          (h(this, er).push(t),
          this.clearGcTimeout(),
          h(this, Xe).notify({
            type: "observerAdded",
            mutation: this,
            observer: t,
          }));
      }
      removeObserver(t) {
        (I(
          this,
          er,
          h(this, er).filter((r) => r !== t)
        ),
          this.scheduleGc(),
          h(this, Xe).notify({
            type: "observerRemoved",
            mutation: this,
            observer: t,
          }));
      }
      optionalRemove() {
        h(this, er).length ||
          (this.state.status === "pending"
            ? this.scheduleGc()
            : h(this, Xe).remove(this));
      }
      continue() {
        return h(this, Fn)?.continue() ?? this.execute(this.state.variables);
      }
      async execute(t) {
        const r = () => {
            G(this, tr, cn).call(this, { type: "continue" });
          },
          n = {
            client: h(this, Wa),
            meta: this.options.meta,
            mutationKey: this.options.mutationKey,
          };
        I(
          this,
          Fn,
          Gf({
            fn: () =>
              this.options.mutationFn
                ? this.options.mutationFn(t, n)
                : Promise.reject(new Error("No mutationFn found")),
            onFail: (o, l) => {
              G(this, tr, cn).call(this, {
                type: "failed",
                failureCount: o,
                error: l,
              });
            },
            onPause: () => {
              G(this, tr, cn).call(this, { type: "pause" });
            },
            onContinue: r,
            retry: this.options.retry ?? 0,
            retryDelay: this.options.retryDelay,
            networkMode: this.options.networkMode,
            canRun: () => h(this, Xe).canRun(this),
          })
        );
        const s = this.state.status === "pending",
          a = !h(this, Fn).canStart();
        try {
          if (s) r();
          else {
            (G(this, tr, cn).call(this, {
              type: "pending",
              variables: t,
              isPaused: a,
            }),
              h(this, Xe).config.onMutate &&
                (await h(this, Xe).config.onMutate(t, this, n)));
            const l = await this.options.onMutate?.(t, n);
            l !== this.state.context &&
              G(this, tr, cn).call(this, {
                type: "pending",
                context: l,
                variables: t,
                isPaused: a,
              });
          }
          const o = await h(this, Fn).start();
          return (
            await h(this, Xe).config.onSuccess?.(
              o,
              t,
              this.state.context,
              this,
              n
            ),
            await this.options.onSuccess?.(o, t, this.state.context, n),
            await h(this, Xe).config.onSettled?.(
              o,
              null,
              this.state.variables,
              this.state.context,
              this,
              n
            ),
            await this.options.onSettled?.(o, null, t, this.state.context, n),
            G(this, tr, cn).call(this, { type: "success", data: o }),
            o
          );
        } catch (o) {
          try {
            await h(this, Xe).config.onError?.(
              o,
              t,
              this.state.context,
              this,
              n
            );
          } catch (l) {
            Promise.reject(l);
          }
          try {
            await this.options.onError?.(o, t, this.state.context, n);
          } catch (l) {
            Promise.reject(l);
          }
          try {
            await h(this, Xe).config.onSettled?.(
              void 0,
              o,
              this.state.variables,
              this.state.context,
              this,
              n
            );
          } catch (l) {
            Promise.reject(l);
          }
          try {
            await this.options.onSettled?.(void 0, o, t, this.state.context, n);
          } catch (l) {
            Promise.reject(l);
          }
          throw (G(this, tr, cn).call(this, { type: "error", error: o }), o);
        } finally {
          h(this, Xe).runNext(this);
        }
      }
    }),
    (Wa = new WeakMap()),
    (er = new WeakMap()),
    (Xe = new WeakMap()),
    (Fn = new WeakMap()),
    (tr = new WeakSet()),
    (cn = function (t) {
      const r = (n) => {
        switch (t.type) {
          case "failed":
            return {
              ...n,
              failureCount: t.failureCount,
              failureReason: t.error,
            };
          case "pause":
            return { ...n, isPaused: !0 };
          case "continue":
            return { ...n, isPaused: !1 };
          case "pending":
            return {
              ...n,
              context: t.context,
              data: void 0,
              failureCount: 0,
              failureReason: null,
              error: null,
              isPaused: t.isPaused,
              status: "pending",
              variables: t.variables,
              submittedAt: Date.now(),
            };
          case "success":
            return {
              ...n,
              data: t.data,
              failureCount: 0,
              failureReason: null,
              error: null,
              status: "success",
              isPaused: !1,
            };
          case "error":
            return {
              ...n,
              data: void 0,
              error: t.error,
              failureCount: n.failureCount + 1,
              failureReason: t.error,
              isPaused: !1,
              status: "error",
            };
        }
      };
      ((this.state = r(this.state)),
        $e.batch(() => {
          (h(this, er).forEach((n) => {
            n.onMutationUpdate(t);
          }),
            h(this, Xe).notify({ mutation: this, type: "updated", action: t }));
        }));
    }),
    hy);
  function ah() {
    return {
      context: void 0,
      data: void 0,
      error: null,
      failureCount: 0,
      failureReason: null,
      isPaused: !1,
      status: "idle",
      variables: void 0,
      submittedAt: 0,
    };
  }
  var yx =
    ((gy = class extends bs {
      constructor(t = {}) {
        super();
        $(this, br);
        $(this, qt);
        $(this, Ha);
        ((this.config = t),
          I(this, br, new Set()),
          I(this, qt, new Map()),
          I(this, Ha, 0));
      }
      build(t, r, n) {
        const s = new gx({
          client: t,
          mutationCache: this,
          mutationId: ++Vi(this, Ha)._,
          options: t.defaultMutationOptions(r),
          state: n,
        });
        return (this.add(s), s);
      }
      add(t) {
        h(this, br).add(t);
        const r = vi(t);
        if (typeof r == "string") {
          const n = h(this, qt).get(r);
          n ? n.push(t) : h(this, qt).set(r, [t]);
        }
        this.notify({ type: "added", mutation: t });
      }
      remove(t) {
        if (h(this, br).delete(t)) {
          const r = vi(t);
          if (typeof r == "string") {
            const n = h(this, qt).get(r);
            if (n)
              if (n.length > 1) {
                const s = n.indexOf(t);
                s !== -1 && n.splice(s, 1);
              } else n[0] === t && h(this, qt).delete(r);
          }
        }
        this.notify({ type: "removed", mutation: t });
      }
      canRun(t) {
        const r = vi(t);
        if (typeof r == "string") {
          const s = h(this, qt)
            .get(r)
            ?.find((a) => a.state.status === "pending");
          return !s || s === t;
        } else return !0;
      }
      runNext(t) {
        const r = vi(t);
        return typeof r == "string"
          ? (h(this, qt)
              .get(r)
              ?.find((s) => s !== t && s.state.isPaused)
              ?.continue() ?? Promise.resolve())
          : Promise.resolve();
      }
      clear() {
        $e.batch(() => {
          (h(this, br).forEach((t) => {
            this.notify({ type: "removed", mutation: t });
          }),
            h(this, br).clear(),
            h(this, qt).clear());
        });
      }
      getAll() {
        return Array.from(h(this, br));
      }
      find(t) {
        const r = { exact: !0, ...t };
        return this.getAll().find((n) => qf(r, n));
      }
      findAll(t = {}) {
        return this.getAll().filter((r) => qf(t, r));
      }
      notify(t) {
        $e.batch(() => {
          this.listeners.forEach((r) => {
            r(t);
          });
        });
      }
      resumePausedMutations() {
        const t = this.getAll().filter((r) => r.state.isPaused);
        return $e.batch(() =>
          Promise.all(t.map((r) => r.continue().catch(st)))
        );
      }
    }),
    (br = new WeakMap()),
    (qt = new WeakMap()),
    (Ha = new WeakMap()),
    gy);
  function vi(e) {
    return e.options.scope?.id;
  }
  var vx =
      ((yy = class extends bs {
        constructor(t, r) {
          super();
          $(this, _r);
          $(this, wr);
          $(this, nn);
          $(this, ht);
          $(this, kr);
          (I(this, wr, t),
            this.setOptions(r),
            this.bindMethods(),
            G(this, _r, Ki).call(this));
        }
        bindMethods() {
          ((this.mutate = this.mutate.bind(this)),
            (this.reset = this.reset.bind(this)));
        }
        setOptions(t) {
          const r = this.options;
          ((this.options = h(this, wr).defaultMutationOptions(t)),
            gi(this.options, r) ||
              h(this, wr)
                .getMutationCache()
                .notify({
                  type: "observerOptionsUpdated",
                  mutation: h(this, ht),
                  observer: this,
                }),
            r?.mutationKey &&
            this.options.mutationKey &&
            Cn(r.mutationKey) !== Cn(this.options.mutationKey)
              ? this.reset()
              : h(this, ht)?.state.status === "pending" &&
                h(this, ht).setOptions(this.options));
        }
        onUnsubscribe() {
          this.hasListeners() || h(this, ht)?.removeObserver(this);
        }
        onMutationUpdate(t) {
          (G(this, _r, Ki).call(this), G(this, _r, hd).call(this, t));
        }
        getCurrentResult() {
          return h(this, nn);
        }
        reset() {
          (h(this, ht)?.removeObserver(this),
            I(this, ht, void 0),
            G(this, _r, Ki).call(this),
            G(this, _r, hd).call(this));
        }
        mutate(t, r) {
          return (
            I(this, kr, r),
            h(this, ht)?.removeObserver(this),
            I(
              this,
              ht,
              h(this, wr).getMutationCache().build(h(this, wr), this.options)
            ),
            h(this, ht).addObserver(this),
            h(this, ht).execute(t)
          );
        }
      }),
      (wr = new WeakMap()),
      (nn = new WeakMap()),
      (ht = new WeakMap()),
      (kr = new WeakMap()),
      (_r = new WeakSet()),
      (Ki = function () {
        const t = h(this, ht)?.state ?? ah();
        I(this, nn, {
          ...t,
          isPending: t.status === "pending",
          isSuccess: t.status === "success",
          isError: t.status === "error",
          isIdle: t.status === "idle",
          mutate: this.mutate,
          reset: this.reset,
        });
      }),
      (hd = function (t) {
        $e.batch(() => {
          if (h(this, kr) && this.hasListeners()) {
            const r = h(this, nn).variables,
              n = h(this, nn).context,
              s = {
                client: h(this, wr),
                meta: this.options.meta,
                mutationKey: this.options.mutationKey,
              };
            if (t?.type === "success") {
              try {
                h(this, kr).onSuccess?.(t.data, r, n, s);
              } catch (a) {
                Promise.reject(a);
              }
              try {
                h(this, kr).onSettled?.(t.data, null, r, n, s);
              } catch (a) {
                Promise.reject(a);
              }
            } else if (t?.type === "error") {
              try {
                h(this, kr).onError?.(t.error, r, n, s);
              } catch (a) {
                Promise.reject(a);
              }
              try {
                h(this, kr).onSettled?.(void 0, t.error, r, n, s);
              } catch (a) {
                Promise.reject(a);
              }
            }
          }
          this.listeners.forEach((r) => {
            r(h(this, nn));
          });
        });
      }),
      yy),
    xx =
      ((vy = class extends bs {
        constructor(t = {}) {
          super();
          $(this, rr);
          ((this.config = t), I(this, rr, new Map()));
        }
        build(t, r, n) {
          const s = r.queryKey,
            a = r.queryHash ?? lu(s, r);
          let o = this.get(a);
          return (
            o ||
              ((o = new dx({
                client: t,
                queryKey: s,
                queryHash: a,
                options: t.defaultQueryOptions(r),
                state: n,
                defaultOptions: t.getQueryDefaults(s),
              })),
              this.add(o)),
            o
          );
        }
        add(t) {
          h(this, rr).has(t.queryHash) ||
            (h(this, rr).set(t.queryHash, t),
            this.notify({ type: "added", query: t }));
        }
        remove(t) {
          const r = h(this, rr).get(t.queryHash);
          r &&
            (t.destroy(),
            r === t && h(this, rr).delete(t.queryHash),
            this.notify({ type: "removed", query: t }));
        }
        clear() {
          $e.batch(() => {
            this.getAll().forEach((t) => {
              this.remove(t);
            });
          });
        }
        get(t) {
          return h(this, rr).get(t);
        }
        getAll() {
          return [...h(this, rr).values()];
        }
        find(t) {
          const r = { exact: !0, ...t };
          return this.getAll().find((n) => Bf(r, n));
        }
        findAll(t = {}) {
          const r = this.getAll();
          return Object.keys(t).length > 0 ? r.filter((n) => Bf(t, n)) : r;
        }
        notify(t) {
          $e.batch(() => {
            this.listeners.forEach((r) => {
              r(t);
            });
          });
        }
        onFocus() {
          $e.batch(() => {
            this.getAll().forEach((t) => {
              t.onFocus();
            });
          });
        }
        onOnline() {
          $e.batch(() => {
            this.getAll().forEach((t) => {
              t.onOnline();
            });
          });
        }
      }),
      (rr = new WeakMap()),
      vy),
    bx =
      ((xy = class {
        constructor(e = {}) {
          $(this, Ee);
          $(this, sn);
          $(this, an);
          $(this, Fs);
          $(this, Us);
          $(this, on);
          $(this, Zs);
          $(this, Bs);
          (I(this, Ee, e.queryCache || new xx()),
            I(this, sn, e.mutationCache || new yx()),
            I(this, an, e.defaultOptions || {}),
            I(this, Fs, new Map()),
            I(this, Us, new Map()),
            I(this, on, 0));
        }
        mount() {
          (Vi(this, on)._++,
            h(this, on) === 1 &&
              (I(
                this,
                Zs,
                ou.subscribe(async (e) => {
                  e &&
                    (await this.resumePausedMutations(), h(this, Ee).onFocus());
                })
              ),
              I(
                this,
                Bs,
                yi.subscribe(async (e) => {
                  e &&
                    (await this.resumePausedMutations(),
                    h(this, Ee).onOnline());
                })
              )));
        }
        unmount() {
          var e, t;
          (Vi(this, on)._--,
            h(this, on) === 0 &&
              ((e = h(this, Zs)) == null || e.call(this),
              I(this, Zs, void 0),
              (t = h(this, Bs)) == null || t.call(this),
              I(this, Bs, void 0)));
        }
        isFetching(e) {
          return h(this, Ee).findAll({ ...e, fetchStatus: "fetching" }).length;
        }
        isMutating(e) {
          return h(this, sn).findAll({ ...e, status: "pending" }).length;
        }
        getQueryData(e) {
          const t = this.defaultQueryOptions({ queryKey: e });
          return h(this, Ee).get(t.queryHash)?.state.data;
        }
        ensureQueryData(e) {
          const t = this.defaultQueryOptions(e),
            r = h(this, Ee).build(this, t),
            n = r.state.data;
          return n === void 0
            ? this.fetchQuery(e)
            : (e.revalidateIfStale &&
                r.isStaleByTime(Vr(t.staleTime, r)) &&
                this.prefetchQuery(t),
              Promise.resolve(n));
        }
        getQueriesData(e) {
          return h(this, Ee)
            .findAll(e)
            .map(({ queryKey: t, state: r }) => {
              const n = r.data;
              return [t, n];
            });
        }
        setQueryData(e, t, r) {
          const n = this.defaultQueryOptions({ queryKey: e }),
            a = h(this, Ee).get(n.queryHash)?.state.data,
            o = tx(t, a);
          if (o !== void 0)
            return h(this, Ee)
              .build(this, n)
              .setData(o, { ...r, manual: !0 });
        }
        setQueriesData(e, t, r) {
          return $e.batch(() =>
            h(this, Ee)
              .findAll(e)
              .map(({ queryKey: n }) => [n, this.setQueryData(n, t, r)])
          );
        }
        getQueryState(e) {
          const t = this.defaultQueryOptions({ queryKey: e });
          return h(this, Ee).get(t.queryHash)?.state;
        }
        removeQueries(e) {
          const t = h(this, Ee);
          $e.batch(() => {
            t.findAll(e).forEach((r) => {
              t.remove(r);
            });
          });
        }
        resetQueries(e, t) {
          const r = h(this, Ee);
          return $e.batch(
            () => (
              r.findAll(e).forEach((n) => {
                n.reset();
              }),
              this.refetchQueries({ type: "active", ...e }, t)
            )
          );
        }
        cancelQueries(e, t = {}) {
          const r = { revert: !0, ...t },
            n = $e.batch(() =>
              h(this, Ee)
                .findAll(e)
                .map((s) => s.cancel(r))
            );
          return Promise.all(n).then(st).catch(st);
        }
        invalidateQueries(e, t = {}) {
          return $e.batch(
            () => (
              h(this, Ee)
                .findAll(e)
                .forEach((r) => {
                  r.invalidate();
                }),
              e?.refetchType === "none"
                ? Promise.resolve()
                : this.refetchQueries(
                    { ...e, type: e?.refetchType ?? e?.type ?? "active" },
                    t
                  )
            )
          );
        }
        refetchQueries(e, t = {}) {
          const r = { ...t, cancelRefetch: t.cancelRefetch ?? !0 },
            n = $e.batch(() =>
              h(this, Ee)
                .findAll(e)
                .filter((s) => !s.isDisabled() && !s.isStatic())
                .map((s) => {
                  let a = s.fetch(void 0, r);
                  return (
                    r.throwOnError || (a = a.catch(st)),
                    s.state.fetchStatus === "paused" ? Promise.resolve() : a
                  );
                })
            );
          return Promise.all(n).then(st);
        }
        fetchQuery(e) {
          const t = this.defaultQueryOptions(e);
          t.retry === void 0 && (t.retry = !1);
          const r = h(this, Ee).build(this, t);
          return r.isStaleByTime(Vr(t.staleTime, r))
            ? r.fetch(t)
            : Promise.resolve(r.state.data);
        }
        prefetchQuery(e) {
          return this.fetchQuery(e).then(st).catch(st);
        }
        fetchInfiniteQuery(e) {
          return ((e.behavior = nh(e.pages)), this.fetchQuery(e));
        }
        prefetchInfiniteQuery(e) {
          return this.fetchInfiniteQuery(e).then(st).catch(st);
        }
        ensureInfiniteQueryData(e) {
          return ((e.behavior = nh(e.pages)), this.ensureQueryData(e));
        }
        resumePausedMutations() {
          return yi.isOnline()
            ? h(this, sn).resumePausedMutations()
            : Promise.resolve();
        }
        getQueryCache() {
          return h(this, Ee);
        }
        getMutationCache() {
          return h(this, sn);
        }
        getDefaultOptions() {
          return h(this, an);
        }
        setDefaultOptions(e) {
          I(this, an, e);
        }
        setQueryDefaults(e, t) {
          h(this, Fs).set(Cn(e), { queryKey: e, defaultOptions: t });
        }
        getQueryDefaults(e) {
          const t = [...h(this, Fs).values()],
            r = {};
          return (
            t.forEach((n) => {
              Ia(e, n.queryKey) && Object.assign(r, n.defaultOptions);
            }),
            r
          );
        }
        setMutationDefaults(e, t) {
          h(this, Us).set(Cn(e), { mutationKey: e, defaultOptions: t });
        }
        getMutationDefaults(e) {
          const t = [...h(this, Us).values()],
            r = {};
          return (
            t.forEach((n) => {
              Ia(e, n.mutationKey) && Object.assign(r, n.defaultOptions);
            }),
            r
          );
        }
        defaultQueryOptions(e) {
          if (e._defaulted) return e;
          const t = {
            ...h(this, an).queries,
            ...this.getQueryDefaults(e.queryKey),
            ...e,
            _defaulted: !0,
          };
          return (
            t.queryHash || (t.queryHash = lu(t.queryKey, t)),
            t.refetchOnReconnect === void 0 &&
              (t.refetchOnReconnect = t.networkMode !== "always"),
            t.throwOnError === void 0 && (t.throwOnError = !!t.suspense),
            !t.networkMode && t.persister && (t.networkMode = "offlineFirst"),
            t.queryFn === du && (t.enabled = !1),
            t
          );
        }
        defaultMutationOptions(e) {
          return e?._defaulted
            ? e
            : {
                ...h(this, an).mutations,
                ...(e?.mutationKey && this.getMutationDefaults(e.mutationKey)),
                ...e,
                _defaulted: !0,
              };
        }
        clear() {
          (h(this, Ee).clear(), h(this, sn).clear());
        }
      }),
      (Ee = new WeakMap()),
      (sn = new WeakMap()),
      (an = new WeakMap()),
      (Fs = new WeakMap()),
      (Us = new WeakMap()),
      (on = new WeakMap()),
      (Zs = new WeakMap()),
      (Bs = new WeakMap()),
      xy),
    oh = R.createContext(void 0),
    fr = (e) => {
      const t = R.useContext(oh);
      if (!t)
        throw new Error(
          "No QueryClient set, use QueryClientProvider to set one"
        );
      return t;
    },
    wx = ({ client: e, children: t }) => (
      R.useEffect(
        () => (
          e.mount(),
          () => {
            e.unmount();
          }
        ),
        [e]
      ),
      i.jsx(oh.Provider, { value: e, children: t })
    ),
    ih = R.createContext(!1),
    kx = () => R.useContext(ih);
  ih.Provider;
  function _x() {
    let e = !1;
    return {
      clearReset: () => {
        e = !1;
      },
      reset: () => {
        e = !0;
      },
      isReset: () => e,
    };
  }
  var Sx = R.createContext(_x()),
    Cx = () => R.useContext(Sx),
    jx = (e, t, r) => {
      const n =
        r?.state.error && typeof e.throwOnError == "function"
          ? pu(e.throwOnError, [r.state.error, r])
          : e.throwOnError;
      (e.suspense || e.experimental_prefetchInRender || n) &&
        (t.isReset() || (e.retryOnMount = !1));
    },
    Nx = (e) => {
      R.useEffect(() => {
        e.clearReset();
      }, [e]);
    },
    Rx = ({
      result: e,
      errorResetBoundary: t,
      throwOnError: r,
      query: n,
      suspense: s,
    }) =>
      e.isError &&
      !t.isReset() &&
      !e.isFetching &&
      n &&
      ((s && e.data === void 0) || pu(r, [e.error, n])),
    Ax = (e) => {
      if (e.suspense) {
        const r = (s) => (s === "static" ? s : Math.max(s ?? 1e3, 1e3)),
          n = e.staleTime;
        ((e.staleTime = typeof n == "function" ? (...s) => r(n(...s)) : r(n)),
          typeof e.gcTime == "number" && (e.gcTime = Math.max(e.gcTime, 1e3)));
      }
    },
    Ex = (e, t) => e.isLoading && e.isFetching && !t,
    Tx = (e, t) => e?.suspense && t.isPending,
    lh = (e, t, r) =>
      t.fetchOptimistic(e).catch(() => {
        r.clearReset();
      });
  function Px(e, t, r) {
    const n = kx(),
      s = Cx(),
      a = fr(),
      o = a.defaultQueryOptions(e);
    a.getDefaultOptions().queries?._experimental_beforeQuery?.(o);
    const l = a.getQueryCache().get(o.queryHash);
    ((o._optimisticResults = n ? "isRestoring" : "optimistic"),
      Ax(o),
      jx(o, s, l),
      Nx(s));
    const c = !a.getQueryCache().get(o.queryHash),
      [u] = R.useState(() => new t(a, o)),
      d = u.getOptimisticResult(o),
      f = !n && e.subscribed !== !1;
    if (
      (R.useSyncExternalStore(
        R.useCallback(
          (m) => {
            const v = f ? u.subscribe($e.batchCalls(m)) : st;
            return (u.updateResult(), v);
          },
          [u, f]
        ),
        () => u.getCurrentResult(),
        () => u.getCurrentResult()
      ),
      R.useEffect(() => {
        u.setOptions(o);
      }, [o, u]),
      Tx(o, d))
    )
      throw lh(o, u, s);
    if (
      Rx({
        result: d,
        errorResetBoundary: s,
        throwOnError: o.throwOnError,
        query: l,
        suspense: o.suspense,
      })
    )
      throw d.error;
    return (
      a.getDefaultOptions().queries?._experimental_afterQuery?.(o, d),
      o.experimental_prefetchInRender &&
        !La.isServer() &&
        Ex(d, n) &&
        (c ? lh(o, u, s) : l?.promise)?.catch(st).finally(() => {
          u.updateResult();
        }),
      o.notifyOnChangeProps ? d : u.trackResult(d)
    );
  }
  function _e(e, t) {
    return Px(e, px);
  }
  function Pt(e, t) {
    const r = fr(),
      [n] = R.useState(() => new vx(r, e));
    R.useEffect(() => {
      n.setOptions(e);
    }, [n, e]);
    const s = R.useSyncExternalStore(
        R.useCallback((o) => n.subscribe($e.batchCalls(o)), [n]),
        () => n.getCurrentResult(),
        () => n.getCurrentResult()
      ),
      a = R.useCallback(
        (o, l) => {
          n.mutate(o, l).catch(st);
        },
        [n]
      );
    if (s.error && pu(n.options.throwOnError, [s.error])) throw s.error;
    return { ...s, mutate: a, mutateAsync: s.mutate };
  }
  var se;
  (function (e) {
    e.assertEqual = (s) => {};
    function t(s) {}
    e.assertIs = t;
    function r(s) {
      throw new Error();
    }
    ((e.assertNever = r),
      (e.arrayToEnum = (s) => {
        const a = {};
        for (const o of s) a[o] = o;
        return a;
      }),
      (e.getValidEnumValues = (s) => {
        const a = e.objectKeys(s).filter((l) => typeof s[s[l]] != "number"),
          o = {};
        for (const l of a) o[l] = s[l];
        return e.objectValues(o);
      }),
      (e.objectValues = (s) =>
        e.objectKeys(s).map(function (a) {
          return s[a];
        })),
      (e.objectKeys =
        typeof Object.keys == "function"
          ? (s) => Object.keys(s)
          : (s) => {
              const a = [];
              for (const o in s)
                Object.prototype.hasOwnProperty.call(s, o) && a.push(o);
              return a;
            }),
      (e.find = (s, a) => {
        for (const o of s) if (a(o)) return o;
      }),
      (e.isInteger =
        typeof Number.isInteger == "function"
          ? (s) => Number.isInteger(s)
          : (s) =>
              typeof s == "number" &&
              Number.isFinite(s) &&
              Math.floor(s) === s));
    function n(s, a = " | ") {
      return s.map((o) => (typeof o == "string" ? `'${o}'` : o)).join(a);
    }
    ((e.joinValues = n),
      (e.jsonStringifyReplacer = (s, a) =>
        typeof a == "bigint" ? a.toString() : a));
  })(se || (se = {}));
  var ch;
  (function (e) {
    e.mergeShapes = (t, r) => ({ ...t, ...r });
  })(ch || (ch = {}));
  const z = se.arrayToEnum([
      "string",
      "nan",
      "number",
      "integer",
      "float",
      "boolean",
      "date",
      "bigint",
      "symbol",
      "function",
      "undefined",
      "null",
      "array",
      "object",
      "unknown",
      "promise",
      "void",
      "never",
      "map",
      "set",
    ]),
    Kr = (e) => {
      switch (typeof e) {
        case "undefined":
          return z.undefined;
        case "string":
          return z.string;
        case "number":
          return Number.isNaN(e) ? z.nan : z.number;
        case "boolean":
          return z.boolean;
        case "function":
          return z.function;
        case "bigint":
          return z.bigint;
        case "symbol":
          return z.symbol;
        case "object":
          return Array.isArray(e)
            ? z.array
            : e === null
              ? z.null
              : e.then &&
                  typeof e.then == "function" &&
                  e.catch &&
                  typeof e.catch == "function"
                ? z.promise
                : typeof Map < "u" && e instanceof Map
                  ? z.map
                  : typeof Set < "u" && e instanceof Set
                    ? z.set
                    : typeof Date < "u" && e instanceof Date
                      ? z.date
                      : z.object;
        default:
          return z.unknown;
      }
    },
    P = se.arrayToEnum([
      "invalid_type",
      "invalid_literal",
      "custom",
      "invalid_union",
      "invalid_union_discriminator",
      "invalid_enum_value",
      "unrecognized_keys",
      "invalid_arguments",
      "invalid_return_type",
      "invalid_date",
      "invalid_string",
      "too_small",
      "too_big",
      "invalid_intersection_types",
      "not_multiple_of",
      "not_finite",
    ]);
  class Jt extends Error {
    get errors() {
      return this.issues;
    }
    constructor(t) {
      (super(),
        (this.issues = []),
        (this.addIssue = (n) => {
          this.issues = [...this.issues, n];
        }),
        (this.addIssues = (n = []) => {
          this.issues = [...this.issues, ...n];
        }));
      const r = new.target.prototype;
      (Object.setPrototypeOf
        ? Object.setPrototypeOf(this, r)
        : (this.__proto__ = r),
        (this.name = "ZodError"),
        (this.issues = t));
    }
    format(t) {
      const r =
          t ||
          function (a) {
            return a.message;
          },
        n = { _errors: [] },
        s = (a) => {
          for (const o of a.issues)
            if (o.code === "invalid_union") o.unionErrors.map(s);
            else if (o.code === "invalid_return_type") s(o.returnTypeError);
            else if (o.code === "invalid_arguments") s(o.argumentsError);
            else if (o.path.length === 0) n._errors.push(r(o));
            else {
              let l = n,
                c = 0;
              for (; c < o.path.length; ) {
                const u = o.path[c];
                (c === o.path.length - 1
                  ? ((l[u] = l[u] || { _errors: [] }), l[u]._errors.push(r(o)))
                  : (l[u] = l[u] || { _errors: [] }),
                  (l = l[u]),
                  c++);
              }
            }
        };
      return (s(this), n);
    }
    static assert(t) {
      if (!(t instanceof Jt)) throw new Error(`Not a ZodError: ${t}`);
    }
    toString() {
      return this.message;
    }
    get message() {
      return JSON.stringify(this.issues, se.jsonStringifyReplacer, 2);
    }
    get isEmpty() {
      return this.issues.length === 0;
    }
    flatten(t = (r) => r.message) {
      const r = {},
        n = [];
      for (const s of this.issues)
        if (s.path.length > 0) {
          const a = s.path[0];
          ((r[a] = r[a] || []), r[a].push(t(s)));
        } else n.push(t(s));
      return { formErrors: n, fieldErrors: r };
    }
    get formErrors() {
      return this.flatten();
    }
  }
  Jt.create = (e) => new Jt(e);
  const yu = (e, t) => {
    let r;
    switch (e.code) {
      case P.invalid_type:
        e.received === z.undefined
          ? (r = "Required")
          : (r = `Expected ${e.expected}, received ${e.received}`);
        break;
      case P.invalid_literal:
        r = `Invalid literal value, expected ${JSON.stringify(e.expected, se.jsonStringifyReplacer)}`;
        break;
      case P.unrecognized_keys:
        r = `Unrecognized key(s) in object: ${se.joinValues(e.keys, ", ")}`;
        break;
      case P.invalid_union:
        r = "Invalid input";
        break;
      case P.invalid_union_discriminator:
        r = `Invalid discriminator value. Expected ${se.joinValues(e.options)}`;
        break;
      case P.invalid_enum_value:
        r = `Invalid enum value. Expected ${se.joinValues(e.options)}, received '${e.received}'`;
        break;
      case P.invalid_arguments:
        r = "Invalid function arguments";
        break;
      case P.invalid_return_type:
        r = "Invalid function return type";
        break;
      case P.invalid_date:
        r = "Invalid date";
        break;
      case P.invalid_string:
        typeof e.validation == "object"
          ? "includes" in e.validation
            ? ((r = `Invalid input: must include "${e.validation.includes}"`),
              typeof e.validation.position == "number" &&
                (r = `${r} at one or more positions greater than or equal to ${e.validation.position}`))
            : "startsWith" in e.validation
              ? (r = `Invalid input: must start with "${e.validation.startsWith}"`)
              : "endsWith" in e.validation
                ? (r = `Invalid input: must end with "${e.validation.endsWith}"`)
                : se.assertNever(e.validation)
          : e.validation !== "regex"
            ? (r = `Invalid ${e.validation}`)
            : (r = "Invalid");
        break;
      case P.too_small:
        e.type === "array"
          ? (r = `Array must contain ${e.exact ? "exactly" : e.inclusive ? "at least" : "more than"} ${e.minimum} element(s)`)
          : e.type === "string"
            ? (r = `String must contain ${e.exact ? "exactly" : e.inclusive ? "at least" : "over"} ${e.minimum} character(s)`)
            : e.type === "number"
              ? (r = `Number must be ${e.exact ? "exactly equal to " : e.inclusive ? "greater than or equal to " : "greater than "}${e.minimum}`)
              : e.type === "bigint"
                ? (r = `Number must be ${e.exact ? "exactly equal to " : e.inclusive ? "greater than or equal to " : "greater than "}${e.minimum}`)
                : e.type === "date"
                  ? (r = `Date must be ${e.exact ? "exactly equal to " : e.inclusive ? "greater than or equal to " : "greater than "}${new Date(Number(e.minimum))}`)
                  : (r = "Invalid input");
        break;
      case P.too_big:
        e.type === "array"
          ? (r = `Array must contain ${e.exact ? "exactly" : e.inclusive ? "at most" : "less than"} ${e.maximum} element(s)`)
          : e.type === "string"
            ? (r = `String must contain ${e.exact ? "exactly" : e.inclusive ? "at most" : "under"} ${e.maximum} character(s)`)
            : e.type === "number"
              ? (r = `Number must be ${e.exact ? "exactly" : e.inclusive ? "less than or equal to" : "less than"} ${e.maximum}`)
              : e.type === "bigint"
                ? (r = `BigInt must be ${e.exact ? "exactly" : e.inclusive ? "less than or equal to" : "less than"} ${e.maximum}`)
                : e.type === "date"
                  ? (r = `Date must be ${e.exact ? "exactly" : e.inclusive ? "smaller than or equal to" : "smaller than"} ${new Date(Number(e.maximum))}`)
                  : (r = "Invalid input");
        break;
      case P.custom:
        r = "Invalid input";
        break;
      case P.invalid_intersection_types:
        r = "Intersection results could not be merged";
        break;
      case P.not_multiple_of:
        r = `Number must be a multiple of ${e.multipleOf}`;
        break;
      case P.not_finite:
        r = "Number must be finite";
        break;
      default:
        ((r = t.defaultError), se.assertNever(e));
    }
    return { message: r };
  };
  let Ox = yu;
  function Ix() {
    return Ox;
  }
  const Lx = (e) => {
    const { data: t, path: r, errorMaps: n, issueData: s } = e,
      a = [...r, ...(s.path || [])],
      o = { ...s, path: a };
    if (s.message !== void 0) return { ...s, path: a, message: s.message };
    let l = "";
    const c = n
      .filter((u) => !!u)
      .slice()
      .reverse();
    for (const u of c) l = u(o, { data: t, defaultError: l }).message;
    return { ...s, path: a, message: l };
  };
  function L(e, t) {
    const r = Ix(),
      n = Lx({
        issueData: t,
        data: e.data,
        path: e.path,
        errorMaps: [
          e.common.contextualErrorMap,
          e.schemaErrorMap,
          r,
          r === yu ? void 0 : yu,
        ].filter((s) => !!s),
      });
    e.common.issues.push(n);
  }
  class at {
    constructor() {
      this.value = "valid";
    }
    dirty() {
      this.value === "valid" && (this.value = "dirty");
    }
    abort() {
      this.value !== "aborted" && (this.value = "aborted");
    }
    static mergeArray(t, r) {
      const n = [];
      for (const s of r) {
        if (s.status === "aborted") return V;
        (s.status === "dirty" && t.dirty(), n.push(s.value));
      }
      return { status: t.value, value: n };
    }
    static async mergeObjectAsync(t, r) {
      const n = [];
      for (const s of r) {
        const a = await s.key,
          o = await s.value;
        n.push({ key: a, value: o });
      }
      return at.mergeObjectSync(t, n);
    }
    static mergeObjectSync(t, r) {
      const n = {};
      for (const s of r) {
        const { key: a, value: o } = s;
        if (a.status === "aborted" || o.status === "aborted") return V;
        (a.status === "dirty" && t.dirty(),
          o.status === "dirty" && t.dirty(),
          a.value !== "__proto__" &&
            (typeof o.value < "u" || s.alwaysSet) &&
            (n[a.value] = o.value));
      }
      return { status: t.value, value: n };
    }
  }
  const V = Object.freeze({ status: "aborted" }),
    Da = (e) => ({ status: "dirty", value: e }),
    Ot = (e) => ({ status: "valid", value: e }),
    uh = (e) => e.status === "aborted",
    dh = (e) => e.status === "dirty",
    ws = (e) => e.status === "valid",
    xi = (e) => typeof Promise < "u" && e instanceof Promise;
  var F;
  (function (e) {
    ((e.errToObj = (t) => (typeof t == "string" ? { message: t } : t || {})),
      (e.toString = (t) => (typeof t == "string" ? t : t?.message)));
  })(F || (F = {}));
  class Xt {
    constructor(t, r, n, s) {
      ((this._cachedPath = []),
        (this.parent = t),
        (this.data = r),
        (this._path = n),
        (this._key = s));
    }
    get path() {
      return (
        this._cachedPath.length ||
          (Array.isArray(this._key)
            ? this._cachedPath.push(...this._path, ...this._key)
            : this._cachedPath.push(...this._path, this._key)),
        this._cachedPath
      );
    }
  }
  const ph = (e, t) => {
    if (ws(t)) return { success: !0, data: t.value };
    if (!e.common.issues.length)
      throw new Error("Validation failed but no issues detected.");
    return {
      success: !1,
      get error() {
        if (this._error) return this._error;
        const r = new Jt(e.common.issues);
        return ((this._error = r), this._error);
      },
    };
  };
  function H(e) {
    if (!e) return {};
    const {
      errorMap: t,
      invalid_type_error: r,
      required_error: n,
      description: s,
    } = e;
    if (t && (r || n))
      throw new Error(
        `Can't use "invalid_type_error" or "required_error" in conjunction with custom error map.`
      );
    return t
      ? { errorMap: t, description: s }
      : {
          errorMap: (o, l) => {
            const { message: c } = e;
            return o.code === "invalid_enum_value"
              ? { message: c ?? l.defaultError }
              : typeof l.data > "u"
                ? { message: c ?? n ?? l.defaultError }
                : o.code !== "invalid_type"
                  ? { message: l.defaultError }
                  : { message: c ?? r ?? l.defaultError };
          },
          description: s,
        };
  }
  let X = class {
    get description() {
      return this._def.description;
    }
    _getType(t) {
      return Kr(t.data);
    }
    _getOrReturnCtx(t, r) {
      return (
        r || {
          common: t.parent.common,
          data: t.data,
          parsedType: Kr(t.data),
          schemaErrorMap: this._def.errorMap,
          path: t.path,
          parent: t.parent,
        }
      );
    }
    _processInputParams(t) {
      return {
        status: new at(),
        ctx: {
          common: t.parent.common,
          data: t.data,
          parsedType: Kr(t.data),
          schemaErrorMap: this._def.errorMap,
          path: t.path,
          parent: t.parent,
        },
      };
    }
    _parseSync(t) {
      const r = this._parse(t);
      if (xi(r)) throw new Error("Synchronous parse encountered promise.");
      return r;
    }
    _parseAsync(t) {
      const r = this._parse(t);
      return Promise.resolve(r);
    }
    parse(t, r) {
      const n = this.safeParse(t, r);
      if (n.success) return n.data;
      throw n.error;
    }
    safeParse(t, r) {
      const n = {
          common: {
            issues: [],
            async: r?.async ?? !1,
            contextualErrorMap: r?.errorMap,
          },
          path: r?.path || [],
          schemaErrorMap: this._def.errorMap,
          parent: null,
          data: t,
          parsedType: Kr(t),
        },
        s = this._parseSync({ data: t, path: n.path, parent: n });
      return ph(n, s);
    }
    "~validate"(t) {
      const r = {
        common: { issues: [], async: !!this["~standard"].async },
        path: [],
        schemaErrorMap: this._def.errorMap,
        parent: null,
        data: t,
        parsedType: Kr(t),
      };
      if (!this["~standard"].async)
        try {
          const n = this._parseSync({ data: t, path: [], parent: r });
          return ws(n) ? { value: n.value } : { issues: r.common.issues };
        } catch (n) {
          (n?.message?.toLowerCase()?.includes("encountered") &&
            (this["~standard"].async = !0),
            (r.common = { issues: [], async: !0 }));
        }
      return this._parseAsync({ data: t, path: [], parent: r }).then((n) =>
        ws(n) ? { value: n.value } : { issues: r.common.issues }
      );
    }
    async parseAsync(t, r) {
      const n = await this.safeParseAsync(t, r);
      if (n.success) return n.data;
      throw n.error;
    }
    async safeParseAsync(t, r) {
      const n = {
          common: { issues: [], contextualErrorMap: r?.errorMap, async: !0 },
          path: r?.path || [],
          schemaErrorMap: this._def.errorMap,
          parent: null,
          data: t,
          parsedType: Kr(t),
        },
        s = this._parse({ data: t, path: n.path, parent: n }),
        a = await (xi(s) ? s : Promise.resolve(s));
      return ph(n, a);
    }
    refine(t, r) {
      const n = (s) =>
        typeof r == "string" || typeof r > "u"
          ? { message: r }
          : typeof r == "function"
            ? r(s)
            : r;
      return this._refinement((s, a) => {
        const o = t(s),
          l = () => a.addIssue({ code: P.custom, ...n(s) });
        return typeof Promise < "u" && o instanceof Promise
          ? o.then((c) => (c ? !0 : (l(), !1)))
          : o
            ? !0
            : (l(), !1);
      });
    }
    refinement(t, r) {
      return this._refinement((n, s) =>
        t(n) ? !0 : (s.addIssue(typeof r == "function" ? r(n, s) : r), !1)
      );
    }
    _refinement(t) {
      return new Nn({
        schema: this,
        typeName: K.ZodEffects,
        effect: { type: "refinement", refinement: t },
      });
    }
    superRefine(t) {
      return this._refinement(t);
    }
    constructor(t) {
      ((this.spa = this.safeParseAsync),
        (this._def = t),
        (this.parse = this.parse.bind(this)),
        (this.safeParse = this.safeParse.bind(this)),
        (this.parseAsync = this.parseAsync.bind(this)),
        (this.safeParseAsync = this.safeParseAsync.bind(this)),
        (this.spa = this.spa.bind(this)),
        (this.refine = this.refine.bind(this)),
        (this.refinement = this.refinement.bind(this)),
        (this.superRefine = this.superRefine.bind(this)),
        (this.optional = this.optional.bind(this)),
        (this.nullable = this.nullable.bind(this)),
        (this.nullish = this.nullish.bind(this)),
        (this.array = this.array.bind(this)),
        (this.promise = this.promise.bind(this)),
        (this.or = this.or.bind(this)),
        (this.and = this.and.bind(this)),
        (this.transform = this.transform.bind(this)),
        (this.brand = this.brand.bind(this)),
        (this.default = this.default.bind(this)),
        (this.catch = this.catch.bind(this)),
        (this.describe = this.describe.bind(this)),
        (this.pipe = this.pipe.bind(this)),
        (this.readonly = this.readonly.bind(this)),
        (this.isNullable = this.isNullable.bind(this)),
        (this.isOptional = this.isOptional.bind(this)),
        (this["~standard"] = {
          version: 1,
          vendor: "zod",
          validate: (r) => this["~validate"](r),
        }));
    }
    optional() {
      return yr.create(this, this._def);
    }
    nullable() {
      return Rn.create(this, this._def);
    }
    nullish() {
      return this.nullable().optional();
    }
    array() {
      return ks.create(this);
    }
    promise() {
      return ji.create(this, this._def);
    }
    or(t) {
      return ki.create([this, t], this._def);
    }
    and(t) {
      return _i.create(this, t, this._def);
    }
    transform(t) {
      return new Nn({
        ...H(this._def),
        schema: this,
        typeName: K.ZodEffects,
        effect: { type: "transform", transform: t },
      });
    }
    default(t) {
      const r = typeof t == "function" ? t : () => t;
      return new Ni({
        ...H(this._def),
        innerType: this,
        defaultValue: r,
        typeName: K.ZodDefault,
      });
    }
    brand() {
      return new wh({ typeName: K.ZodBranded, type: this, ...H(this._def) });
    }
    catch(t) {
      const r = typeof t == "function" ? t : () => t;
      return new Ri({
        ...H(this._def),
        innerType: this,
        catchValue: r,
        typeName: K.ZodCatch,
      });
    }
    describe(t) {
      const r = this.constructor;
      return new r({ ...this._def, description: t });
    }
    pipe(t) {
      return Ru.create(this, t);
    }
    readonly() {
      return Ai.create(this);
    }
    isOptional() {
      return this.safeParse(void 0).success;
    }
    isNullable() {
      return this.safeParse(null).success;
    }
  };
  const Dx = /^c[^\s-]{8,}$/i,
    zx = /^[0-9a-z]+$/,
    Mx = /^[0-9A-HJKMNP-TV-Z]{26}$/i,
    $x =
      /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i,
    Fx = /^[a-z0-9_-]{21}$/i,
    Ux = /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/,
    Zx =
      /^[-+]?P(?!$)(?:(?:[-+]?\d+Y)|(?:[-+]?\d+[.,]\d+Y$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:(?:[-+]?\d+W)|(?:[-+]?\d+[.,]\d+W$))?(?:(?:[-+]?\d+D)|(?:[-+]?\d+[.,]\d+D$))?(?:T(?=[\d+-])(?:(?:[-+]?\d+H)|(?:[-+]?\d+[.,]\d+H$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:[-+]?\d+(?:[.,]\d+)?S)?)??$/,
    Bx =
      /^(?!\.)(?!.*\.\.)([A-Z0-9_'+\-\.]*)[A-Z0-9_+-]@([A-Z0-9][A-Z0-9\-]*\.)+[A-Z]{2,}$/i,
    qx = "^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$";
  let vu;
  const Vx =
      /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/,
    Kx =
      /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/(3[0-2]|[12]?[0-9])$/,
    Wx =
      /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/,
    Hx =
      /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/,
    Qx = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/,
    Gx =
      /^([0-9a-zA-Z-_]{4})*(([0-9a-zA-Z-_]{2}(==)?)|([0-9a-zA-Z-_]{3}(=)?))?$/,
    mh =
      "((\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-((0[13578]|1[02])-(0[1-9]|[12]\\d|3[01])|(0[469]|11)-(0[1-9]|[12]\\d|30)|(02)-(0[1-9]|1\\d|2[0-8])))",
    Yx = new RegExp(`^${mh}$`);
  function fh(e) {
    let t = "[0-5]\\d";
    e.precision
      ? (t = `${t}\\.\\d{${e.precision}}`)
      : e.precision == null && (t = `${t}(\\.\\d+)?`);
    const r = e.precision ? "+" : "?";
    return `([01]\\d|2[0-3]):[0-5]\\d(:${t})${r}`;
  }
  function Jx(e) {
    return new RegExp(`^${fh(e)}$`);
  }
  function Xx(e) {
    let t = `${mh}T${fh(e)}`;
    const r = [];
    return (
      r.push(e.local ? "Z?" : "Z"),
      e.offset && r.push("([+-]\\d{2}:?\\d{2})"),
      (t = `${t}(${r.join("|")})`),
      new RegExp(`^${t}$`)
    );
  }
  function eb(e, t) {
    return !!(
      ((t === "v4" || !t) && Vx.test(e)) ||
      ((t === "v6" || !t) && Wx.test(e))
    );
  }
  function tb(e, t) {
    if (!Ux.test(e)) return !1;
    try {
      const [r] = e.split(".");
      if (!r) return !1;
      const n = r
          .replace(/-/g, "+")
          .replace(/_/g, "/")
          .padEnd(r.length + ((4 - (r.length % 4)) % 4), "="),
        s = JSON.parse(atob(n));
      return !(
        typeof s != "object" ||
        s === null ||
        ("typ" in s && s?.typ !== "JWT") ||
        !s.alg ||
        (t && s.alg !== t)
      );
    } catch {
      return !1;
    }
  }
  function rb(e, t) {
    return !!(
      ((t === "v4" || !t) && Kx.test(e)) ||
      ((t === "v6" || !t) && Hx.test(e))
    );
  }
  let bi = class Xa extends X {
    _parse(t) {
      if (
        (this._def.coerce && (t.data = String(t.data)),
        this._getType(t) !== z.string)
      ) {
        const a = this._getOrReturnCtx(t);
        return (
          L(a, {
            code: P.invalid_type,
            expected: z.string,
            received: a.parsedType,
          }),
          V
        );
      }
      const n = new at();
      let s;
      for (const a of this._def.checks)
        if (a.kind === "min")
          t.data.length < a.value &&
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              code: P.too_small,
              minimum: a.value,
              type: "string",
              inclusive: !0,
              exact: !1,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "max")
          t.data.length > a.value &&
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              code: P.too_big,
              maximum: a.value,
              type: "string",
              inclusive: !0,
              exact: !1,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "length") {
          const o = t.data.length > a.value,
            l = t.data.length < a.value;
          (o || l) &&
            ((s = this._getOrReturnCtx(t, s)),
            o
              ? L(s, {
                  code: P.too_big,
                  maximum: a.value,
                  type: "string",
                  inclusive: !0,
                  exact: !0,
                  message: a.message,
                })
              : l &&
                L(s, {
                  code: P.too_small,
                  minimum: a.value,
                  type: "string",
                  inclusive: !0,
                  exact: !0,
                  message: a.message,
                }),
            n.dirty());
        } else if (a.kind === "email")
          Bx.test(t.data) ||
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              validation: "email",
              code: P.invalid_string,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "emoji")
          (vu || (vu = new RegExp(qx, "u")),
            vu.test(t.data) ||
              ((s = this._getOrReturnCtx(t, s)),
              L(s, {
                validation: "emoji",
                code: P.invalid_string,
                message: a.message,
              }),
              n.dirty()));
        else if (a.kind === "uuid")
          $x.test(t.data) ||
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              validation: "uuid",
              code: P.invalid_string,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "nanoid")
          Fx.test(t.data) ||
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              validation: "nanoid",
              code: P.invalid_string,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "cuid")
          Dx.test(t.data) ||
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              validation: "cuid",
              code: P.invalid_string,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "cuid2")
          zx.test(t.data) ||
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              validation: "cuid2",
              code: P.invalid_string,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "ulid")
          Mx.test(t.data) ||
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              validation: "ulid",
              code: P.invalid_string,
              message: a.message,
            }),
            n.dirty());
        else if (a.kind === "url")
          try {
            new URL(t.data);
          } catch {
            ((s = this._getOrReturnCtx(t, s)),
              L(s, {
                validation: "url",
                code: P.invalid_string,
                message: a.message,
              }),
              n.dirty());
          }
        else
          a.kind === "regex"
            ? ((a.regex.lastIndex = 0),
              a.regex.test(t.data) ||
                ((s = this._getOrReturnCtx(t, s)),
                L(s, {
                  validation: "regex",
                  code: P.invalid_string,
                  message: a.message,
                }),
                n.dirty()))
            : a.kind === "trim"
              ? (t.data = t.data.trim())
              : a.kind === "includes"
                ? t.data.includes(a.value, a.position) ||
                  ((s = this._getOrReturnCtx(t, s)),
                  L(s, {
                    code: P.invalid_string,
                    validation: { includes: a.value, position: a.position },
                    message: a.message,
                  }),
                  n.dirty())
                : a.kind === "toLowerCase"
                  ? (t.data = t.data.toLowerCase())
                  : a.kind === "toUpperCase"
                    ? (t.data = t.data.toUpperCase())
                    : a.kind === "startsWith"
                      ? t.data.startsWith(a.value) ||
                        ((s = this._getOrReturnCtx(t, s)),
                        L(s, {
                          code: P.invalid_string,
                          validation: { startsWith: a.value },
                          message: a.message,
                        }),
                        n.dirty())
                      : a.kind === "endsWith"
                        ? t.data.endsWith(a.value) ||
                          ((s = this._getOrReturnCtx(t, s)),
                          L(s, {
                            code: P.invalid_string,
                            validation: { endsWith: a.value },
                            message: a.message,
                          }),
                          n.dirty())
                        : a.kind === "datetime"
                          ? Xx(a).test(t.data) ||
                            ((s = this._getOrReturnCtx(t, s)),
                            L(s, {
                              code: P.invalid_string,
                              validation: "datetime",
                              message: a.message,
                            }),
                            n.dirty())
                          : a.kind === "date"
                            ? Yx.test(t.data) ||
                              ((s = this._getOrReturnCtx(t, s)),
                              L(s, {
                                code: P.invalid_string,
                                validation: "date",
                                message: a.message,
                              }),
                              n.dirty())
                            : a.kind === "time"
                              ? Jx(a).test(t.data) ||
                                ((s = this._getOrReturnCtx(t, s)),
                                L(s, {
                                  code: P.invalid_string,
                                  validation: "time",
                                  message: a.message,
                                }),
                                n.dirty())
                              : a.kind === "duration"
                                ? Zx.test(t.data) ||
                                  ((s = this._getOrReturnCtx(t, s)),
                                  L(s, {
                                    validation: "duration",
                                    code: P.invalid_string,
                                    message: a.message,
                                  }),
                                  n.dirty())
                                : a.kind === "ip"
                                  ? eb(t.data, a.version) ||
                                    ((s = this._getOrReturnCtx(t, s)),
                                    L(s, {
                                      validation: "ip",
                                      code: P.invalid_string,
                                      message: a.message,
                                    }),
                                    n.dirty())
                                  : a.kind === "jwt"
                                    ? tb(t.data, a.alg) ||
                                      ((s = this._getOrReturnCtx(t, s)),
                                      L(s, {
                                        validation: "jwt",
                                        code: P.invalid_string,
                                        message: a.message,
                                      }),
                                      n.dirty())
                                    : a.kind === "cidr"
                                      ? rb(t.data, a.version) ||
                                        ((s = this._getOrReturnCtx(t, s)),
                                        L(s, {
                                          validation: "cidr",
                                          code: P.invalid_string,
                                          message: a.message,
                                        }),
                                        n.dirty())
                                      : a.kind === "base64"
                                        ? Qx.test(t.data) ||
                                          ((s = this._getOrReturnCtx(t, s)),
                                          L(s, {
                                            validation: "base64",
                                            code: P.invalid_string,
                                            message: a.message,
                                          }),
                                          n.dirty())
                                        : a.kind === "base64url"
                                          ? Gx.test(t.data) ||
                                            ((s = this._getOrReturnCtx(t, s)),
                                            L(s, {
                                              validation: "base64url",
                                              code: P.invalid_string,
                                              message: a.message,
                                            }),
                                            n.dirty())
                                          : se.assertNever(a);
      return { status: n.value, value: t.data };
    }
    _regex(t, r, n) {
      return this.refinement((s) => t.test(s), {
        validation: r,
        code: P.invalid_string,
        ...F.errToObj(n),
      });
    }
    _addCheck(t) {
      return new Xa({ ...this._def, checks: [...this._def.checks, t] });
    }
    email(t) {
      return this._addCheck({ kind: "email", ...F.errToObj(t) });
    }
    url(t) {
      return this._addCheck({ kind: "url", ...F.errToObj(t) });
    }
    emoji(t) {
      return this._addCheck({ kind: "emoji", ...F.errToObj(t) });
    }
    uuid(t) {
      return this._addCheck({ kind: "uuid", ...F.errToObj(t) });
    }
    nanoid(t) {
      return this._addCheck({ kind: "nanoid", ...F.errToObj(t) });
    }
    cuid(t) {
      return this._addCheck({ kind: "cuid", ...F.errToObj(t) });
    }
    cuid2(t) {
      return this._addCheck({ kind: "cuid2", ...F.errToObj(t) });
    }
    ulid(t) {
      return this._addCheck({ kind: "ulid", ...F.errToObj(t) });
    }
    base64(t) {
      return this._addCheck({ kind: "base64", ...F.errToObj(t) });
    }
    base64url(t) {
      return this._addCheck({ kind: "base64url", ...F.errToObj(t) });
    }
    jwt(t) {
      return this._addCheck({ kind: "jwt", ...F.errToObj(t) });
    }
    ip(t) {
      return this._addCheck({ kind: "ip", ...F.errToObj(t) });
    }
    cidr(t) {
      return this._addCheck({ kind: "cidr", ...F.errToObj(t) });
    }
    datetime(t) {
      return typeof t == "string"
        ? this._addCheck({
            kind: "datetime",
            precision: null,
            offset: !1,
            local: !1,
            message: t,
          })
        : this._addCheck({
            kind: "datetime",
            precision: typeof t?.precision > "u" ? null : t?.precision,
            offset: t?.offset ?? !1,
            local: t?.local ?? !1,
            ...F.errToObj(t?.message),
          });
    }
    date(t) {
      return this._addCheck({ kind: "date", message: t });
    }
    time(t) {
      return typeof t == "string"
        ? this._addCheck({ kind: "time", precision: null, message: t })
        : this._addCheck({
            kind: "time",
            precision: typeof t?.precision > "u" ? null : t?.precision,
            ...F.errToObj(t?.message),
          });
    }
    duration(t) {
      return this._addCheck({ kind: "duration", ...F.errToObj(t) });
    }
    regex(t, r) {
      return this._addCheck({ kind: "regex", regex: t, ...F.errToObj(r) });
    }
    includes(t, r) {
      return this._addCheck({
        kind: "includes",
        value: t,
        position: r?.position,
        ...F.errToObj(r?.message),
      });
    }
    startsWith(t, r) {
      return this._addCheck({ kind: "startsWith", value: t, ...F.errToObj(r) });
    }
    endsWith(t, r) {
      return this._addCheck({ kind: "endsWith", value: t, ...F.errToObj(r) });
    }
    min(t, r) {
      return this._addCheck({ kind: "min", value: t, ...F.errToObj(r) });
    }
    max(t, r) {
      return this._addCheck({ kind: "max", value: t, ...F.errToObj(r) });
    }
    length(t, r) {
      return this._addCheck({ kind: "length", value: t, ...F.errToObj(r) });
    }
    nonempty(t) {
      return this.min(1, F.errToObj(t));
    }
    trim() {
      return new Xa({
        ...this._def,
        checks: [...this._def.checks, { kind: "trim" }],
      });
    }
    toLowerCase() {
      return new Xa({
        ...this._def,
        checks: [...this._def.checks, { kind: "toLowerCase" }],
      });
    }
    toUpperCase() {
      return new Xa({
        ...this._def,
        checks: [...this._def.checks, { kind: "toUpperCase" }],
      });
    }
    get isDatetime() {
      return !!this._def.checks.find((t) => t.kind === "datetime");
    }
    get isDate() {
      return !!this._def.checks.find((t) => t.kind === "date");
    }
    get isTime() {
      return !!this._def.checks.find((t) => t.kind === "time");
    }
    get isDuration() {
      return !!this._def.checks.find((t) => t.kind === "duration");
    }
    get isEmail() {
      return !!this._def.checks.find((t) => t.kind === "email");
    }
    get isURL() {
      return !!this._def.checks.find((t) => t.kind === "url");
    }
    get isEmoji() {
      return !!this._def.checks.find((t) => t.kind === "emoji");
    }
    get isUUID() {
      return !!this._def.checks.find((t) => t.kind === "uuid");
    }
    get isNANOID() {
      return !!this._def.checks.find((t) => t.kind === "nanoid");
    }
    get isCUID() {
      return !!this._def.checks.find((t) => t.kind === "cuid");
    }
    get isCUID2() {
      return !!this._def.checks.find((t) => t.kind === "cuid2");
    }
    get isULID() {
      return !!this._def.checks.find((t) => t.kind === "ulid");
    }
    get isIP() {
      return !!this._def.checks.find((t) => t.kind === "ip");
    }
    get isCIDR() {
      return !!this._def.checks.find((t) => t.kind === "cidr");
    }
    get isBase64() {
      return !!this._def.checks.find((t) => t.kind === "base64");
    }
    get isBase64url() {
      return !!this._def.checks.find((t) => t.kind === "base64url");
    }
    get minLength() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "min" && (t === null || r.value > t) && (t = r.value);
      return t;
    }
    get maxLength() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "max" && (t === null || r.value < t) && (t = r.value);
      return t;
    }
  };
  bi.create = (e) =>
    new bi({
      checks: [],
      typeName: K.ZodString,
      coerce: e?.coerce ?? !1,
      ...H(e),
    });
  function nb(e, t) {
    const r = (e.toString().split(".")[1] || "").length,
      n = (t.toString().split(".")[1] || "").length,
      s = r > n ? r : n,
      a = Number.parseInt(e.toFixed(s).replace(".", "")),
      o = Number.parseInt(t.toFixed(s).replace(".", ""));
    return (a % o) / 10 ** s;
  }
  let xu = class gd extends X {
    constructor() {
      (super(...arguments),
        (this.min = this.gte),
        (this.max = this.lte),
        (this.step = this.multipleOf));
    }
    _parse(t) {
      if (
        (this._def.coerce && (t.data = Number(t.data)),
        this._getType(t) !== z.number)
      ) {
        const a = this._getOrReturnCtx(t);
        return (
          L(a, {
            code: P.invalid_type,
            expected: z.number,
            received: a.parsedType,
          }),
          V
        );
      }
      let n;
      const s = new at();
      for (const a of this._def.checks)
        a.kind === "int"
          ? se.isInteger(t.data) ||
            ((n = this._getOrReturnCtx(t, n)),
            L(n, {
              code: P.invalid_type,
              expected: "integer",
              received: "float",
              message: a.message,
            }),
            s.dirty())
          : a.kind === "min"
            ? (a.inclusive ? t.data < a.value : t.data <= a.value) &&
              ((n = this._getOrReturnCtx(t, n)),
              L(n, {
                code: P.too_small,
                minimum: a.value,
                type: "number",
                inclusive: a.inclusive,
                exact: !1,
                message: a.message,
              }),
              s.dirty())
            : a.kind === "max"
              ? (a.inclusive ? t.data > a.value : t.data >= a.value) &&
                ((n = this._getOrReturnCtx(t, n)),
                L(n, {
                  code: P.too_big,
                  maximum: a.value,
                  type: "number",
                  inclusive: a.inclusive,
                  exact: !1,
                  message: a.message,
                }),
                s.dirty())
              : a.kind === "multipleOf"
                ? nb(t.data, a.value) !== 0 &&
                  ((n = this._getOrReturnCtx(t, n)),
                  L(n, {
                    code: P.not_multiple_of,
                    multipleOf: a.value,
                    message: a.message,
                  }),
                  s.dirty())
                : a.kind === "finite"
                  ? Number.isFinite(t.data) ||
                    ((n = this._getOrReturnCtx(t, n)),
                    L(n, { code: P.not_finite, message: a.message }),
                    s.dirty())
                  : se.assertNever(a);
      return { status: s.value, value: t.data };
    }
    gte(t, r) {
      return this.setLimit("min", t, !0, F.toString(r));
    }
    gt(t, r) {
      return this.setLimit("min", t, !1, F.toString(r));
    }
    lte(t, r) {
      return this.setLimit("max", t, !0, F.toString(r));
    }
    lt(t, r) {
      return this.setLimit("max", t, !1, F.toString(r));
    }
    setLimit(t, r, n, s) {
      return new gd({
        ...this._def,
        checks: [
          ...this._def.checks,
          { kind: t, value: r, inclusive: n, message: F.toString(s) },
        ],
      });
    }
    _addCheck(t) {
      return new gd({ ...this._def, checks: [...this._def.checks, t] });
    }
    int(t) {
      return this._addCheck({ kind: "int", message: F.toString(t) });
    }
    positive(t) {
      return this._addCheck({
        kind: "min",
        value: 0,
        inclusive: !1,
        message: F.toString(t),
      });
    }
    negative(t) {
      return this._addCheck({
        kind: "max",
        value: 0,
        inclusive: !1,
        message: F.toString(t),
      });
    }
    nonpositive(t) {
      return this._addCheck({
        kind: "max",
        value: 0,
        inclusive: !0,
        message: F.toString(t),
      });
    }
    nonnegative(t) {
      return this._addCheck({
        kind: "min",
        value: 0,
        inclusive: !0,
        message: F.toString(t),
      });
    }
    multipleOf(t, r) {
      return this._addCheck({
        kind: "multipleOf",
        value: t,
        message: F.toString(r),
      });
    }
    finite(t) {
      return this._addCheck({ kind: "finite", message: F.toString(t) });
    }
    safe(t) {
      return this._addCheck({
        kind: "min",
        inclusive: !0,
        value: Number.MIN_SAFE_INTEGER,
        message: F.toString(t),
      })._addCheck({
        kind: "max",
        inclusive: !0,
        value: Number.MAX_SAFE_INTEGER,
        message: F.toString(t),
      });
    }
    get minValue() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "min" && (t === null || r.value > t) && (t = r.value);
      return t;
    }
    get maxValue() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "max" && (t === null || r.value < t) && (t = r.value);
      return t;
    }
    get isInt() {
      return !!this._def.checks.find(
        (t) =>
          t.kind === "int" || (t.kind === "multipleOf" && se.isInteger(t.value))
      );
    }
    get isFinite() {
      let t = null,
        r = null;
      for (const n of this._def.checks) {
        if (n.kind === "finite" || n.kind === "int" || n.kind === "multipleOf")
          return !0;
        n.kind === "min"
          ? (r === null || n.value > r) && (r = n.value)
          : n.kind === "max" && (t === null || n.value < t) && (t = n.value);
      }
      return Number.isFinite(r) && Number.isFinite(t);
    }
  };
  xu.create = (e) =>
    new xu({
      checks: [],
      typeName: K.ZodNumber,
      coerce: e?.coerce || !1,
      ...H(e),
    });
  class za extends X {
    constructor() {
      (super(...arguments), (this.min = this.gte), (this.max = this.lte));
    }
    _parse(t) {
      if (this._def.coerce)
        try {
          t.data = BigInt(t.data);
        } catch {
          return this._getInvalidInput(t);
        }
      if (this._getType(t) !== z.bigint) return this._getInvalidInput(t);
      let n;
      const s = new at();
      for (const a of this._def.checks)
        a.kind === "min"
          ? (a.inclusive ? t.data < a.value : t.data <= a.value) &&
            ((n = this._getOrReturnCtx(t, n)),
            L(n, {
              code: P.too_small,
              type: "bigint",
              minimum: a.value,
              inclusive: a.inclusive,
              message: a.message,
            }),
            s.dirty())
          : a.kind === "max"
            ? (a.inclusive ? t.data > a.value : t.data >= a.value) &&
              ((n = this._getOrReturnCtx(t, n)),
              L(n, {
                code: P.too_big,
                type: "bigint",
                maximum: a.value,
                inclusive: a.inclusive,
                message: a.message,
              }),
              s.dirty())
            : a.kind === "multipleOf"
              ? t.data % a.value !== BigInt(0) &&
                ((n = this._getOrReturnCtx(t, n)),
                L(n, {
                  code: P.not_multiple_of,
                  multipleOf: a.value,
                  message: a.message,
                }),
                s.dirty())
              : se.assertNever(a);
      return { status: s.value, value: t.data };
    }
    _getInvalidInput(t) {
      const r = this._getOrReturnCtx(t);
      return (
        L(r, {
          code: P.invalid_type,
          expected: z.bigint,
          received: r.parsedType,
        }),
        V
      );
    }
    gte(t, r) {
      return this.setLimit("min", t, !0, F.toString(r));
    }
    gt(t, r) {
      return this.setLimit("min", t, !1, F.toString(r));
    }
    lte(t, r) {
      return this.setLimit("max", t, !0, F.toString(r));
    }
    lt(t, r) {
      return this.setLimit("max", t, !1, F.toString(r));
    }
    setLimit(t, r, n, s) {
      return new za({
        ...this._def,
        checks: [
          ...this._def.checks,
          { kind: t, value: r, inclusive: n, message: F.toString(s) },
        ],
      });
    }
    _addCheck(t) {
      return new za({ ...this._def, checks: [...this._def.checks, t] });
    }
    positive(t) {
      return this._addCheck({
        kind: "min",
        value: BigInt(0),
        inclusive: !1,
        message: F.toString(t),
      });
    }
    negative(t) {
      return this._addCheck({
        kind: "max",
        value: BigInt(0),
        inclusive: !1,
        message: F.toString(t),
      });
    }
    nonpositive(t) {
      return this._addCheck({
        kind: "max",
        value: BigInt(0),
        inclusive: !0,
        message: F.toString(t),
      });
    }
    nonnegative(t) {
      return this._addCheck({
        kind: "min",
        value: BigInt(0),
        inclusive: !0,
        message: F.toString(t),
      });
    }
    multipleOf(t, r) {
      return this._addCheck({
        kind: "multipleOf",
        value: t,
        message: F.toString(r),
      });
    }
    get minValue() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "min" && (t === null || r.value > t) && (t = r.value);
      return t;
    }
    get maxValue() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "max" && (t === null || r.value < t) && (t = r.value);
      return t;
    }
  }
  za.create = (e) =>
    new za({
      checks: [],
      typeName: K.ZodBigInt,
      coerce: e?.coerce ?? !1,
      ...H(e),
    });
  let bu = class extends X {
    _parse(t) {
      if (
        (this._def.coerce && (t.data = !!t.data),
        this._getType(t) !== z.boolean)
      ) {
        const n = this._getOrReturnCtx(t);
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.boolean,
            received: n.parsedType,
          }),
          V
        );
      }
      return Ot(t.data);
    }
  };
  bu.create = (e) =>
    new bu({ typeName: K.ZodBoolean, coerce: e?.coerce || !1, ...H(e) });
  class wi extends X {
    _parse(t) {
      if (
        (this._def.coerce && (t.data = new Date(t.data)),
        this._getType(t) !== z.date)
      ) {
        const a = this._getOrReturnCtx(t);
        return (
          L(a, {
            code: P.invalid_type,
            expected: z.date,
            received: a.parsedType,
          }),
          V
        );
      }
      if (Number.isNaN(t.data.getTime())) {
        const a = this._getOrReturnCtx(t);
        return (L(a, { code: P.invalid_date }), V);
      }
      const n = new at();
      let s;
      for (const a of this._def.checks)
        a.kind === "min"
          ? t.data.getTime() < a.value &&
            ((s = this._getOrReturnCtx(t, s)),
            L(s, {
              code: P.too_small,
              message: a.message,
              inclusive: !0,
              exact: !1,
              minimum: a.value,
              type: "date",
            }),
            n.dirty())
          : a.kind === "max"
            ? t.data.getTime() > a.value &&
              ((s = this._getOrReturnCtx(t, s)),
              L(s, {
                code: P.too_big,
                message: a.message,
                inclusive: !0,
                exact: !1,
                maximum: a.value,
                type: "date",
              }),
              n.dirty())
            : se.assertNever(a);
      return { status: n.value, value: new Date(t.data.getTime()) };
    }
    _addCheck(t) {
      return new wi({ ...this._def, checks: [...this._def.checks, t] });
    }
    min(t, r) {
      return this._addCheck({
        kind: "min",
        value: t.getTime(),
        message: F.toString(r),
      });
    }
    max(t, r) {
      return this._addCheck({
        kind: "max",
        value: t.getTime(),
        message: F.toString(r),
      });
    }
    get minDate() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "min" && (t === null || r.value > t) && (t = r.value);
      return t != null ? new Date(t) : null;
    }
    get maxDate() {
      let t = null;
      for (const r of this._def.checks)
        r.kind === "max" && (t === null || r.value < t) && (t = r.value);
      return t != null ? new Date(t) : null;
    }
  }
  wi.create = (e) =>
    new wi({
      checks: [],
      coerce: e?.coerce || !1,
      typeName: K.ZodDate,
      ...H(e),
    });
  class hh extends X {
    _parse(t) {
      if (this._getType(t) !== z.symbol) {
        const n = this._getOrReturnCtx(t);
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.symbol,
            received: n.parsedType,
          }),
          V
        );
      }
      return Ot(t.data);
    }
  }
  hh.create = (e) => new hh({ typeName: K.ZodSymbol, ...H(e) });
  class wu extends X {
    _parse(t) {
      if (this._getType(t) !== z.undefined) {
        const n = this._getOrReturnCtx(t);
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.undefined,
            received: n.parsedType,
          }),
          V
        );
      }
      return Ot(t.data);
    }
  }
  wu.create = (e) => new wu({ typeName: K.ZodUndefined, ...H(e) });
  class ku extends X {
    _parse(t) {
      if (this._getType(t) !== z.null) {
        const n = this._getOrReturnCtx(t);
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.null,
            received: n.parsedType,
          }),
          V
        );
      }
      return Ot(t.data);
    }
  }
  ku.create = (e) => new ku({ typeName: K.ZodNull, ...H(e) });
  let gh = class extends X {
    constructor() {
      (super(...arguments), (this._any = !0));
    }
    _parse(t) {
      return Ot(t.data);
    }
  };
  gh.create = (e) => new gh({ typeName: K.ZodAny, ...H(e) });
  let _u = class extends X {
    constructor() {
      (super(...arguments), (this._unknown = !0));
    }
    _parse(t) {
      return Ot(t.data);
    }
  };
  _u.create = (e) => new _u({ typeName: K.ZodUnknown, ...H(e) });
  let Wr = class extends X {
    _parse(t) {
      const r = this._getOrReturnCtx(t);
      return (
        L(r, {
          code: P.invalid_type,
          expected: z.never,
          received: r.parsedType,
        }),
        V
      );
    }
  };
  Wr.create = (e) => new Wr({ typeName: K.ZodNever, ...H(e) });
  class yh extends X {
    _parse(t) {
      if (this._getType(t) !== z.undefined) {
        const n = this._getOrReturnCtx(t);
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.void,
            received: n.parsedType,
          }),
          V
        );
      }
      return Ot(t.data);
    }
  }
  yh.create = (e) => new yh({ typeName: K.ZodVoid, ...H(e) });
  let ks = class Wi extends X {
    _parse(t) {
      const { ctx: r, status: n } = this._processInputParams(t),
        s = this._def;
      if (r.parsedType !== z.array)
        return (
          L(r, {
            code: P.invalid_type,
            expected: z.array,
            received: r.parsedType,
          }),
          V
        );
      if (s.exactLength !== null) {
        const o = r.data.length > s.exactLength.value,
          l = r.data.length < s.exactLength.value;
        (o || l) &&
          (L(r, {
            code: o ? P.too_big : P.too_small,
            minimum: l ? s.exactLength.value : void 0,
            maximum: o ? s.exactLength.value : void 0,
            type: "array",
            inclusive: !0,
            exact: !0,
            message: s.exactLength.message,
          }),
          n.dirty());
      }
      if (
        (s.minLength !== null &&
          r.data.length < s.minLength.value &&
          (L(r, {
            code: P.too_small,
            minimum: s.minLength.value,
            type: "array",
            inclusive: !0,
            exact: !1,
            message: s.minLength.message,
          }),
          n.dirty()),
        s.maxLength !== null &&
          r.data.length > s.maxLength.value &&
          (L(r, {
            code: P.too_big,
            maximum: s.maxLength.value,
            type: "array",
            inclusive: !0,
            exact: !1,
            message: s.maxLength.message,
          }),
          n.dirty()),
        r.common.async)
      )
        return Promise.all(
          [...r.data].map((o, l) => s.type._parseAsync(new Xt(r, o, r.path, l)))
        ).then((o) => at.mergeArray(n, o));
      const a = [...r.data].map((o, l) =>
        s.type._parseSync(new Xt(r, o, r.path, l))
      );
      return at.mergeArray(n, a);
    }
    get element() {
      return this._def.type;
    }
    min(t, r) {
      return new Wi({
        ...this._def,
        minLength: { value: t, message: F.toString(r) },
      });
    }
    max(t, r) {
      return new Wi({
        ...this._def,
        maxLength: { value: t, message: F.toString(r) },
      });
    }
    length(t, r) {
      return new Wi({
        ...this._def,
        exactLength: { value: t, message: F.toString(r) },
      });
    }
    nonempty(t) {
      return this.min(1, t);
    }
  };
  ks.create = (e, t) =>
    new ks({
      type: e,
      minLength: null,
      maxLength: null,
      exactLength: null,
      typeName: K.ZodArray,
      ...H(t),
    });
  function _s(e) {
    if (e instanceof hr) {
      const t = {};
      for (const r in e.shape) {
        const n = e.shape[r];
        t[r] = yr.create(_s(n));
      }
      return new hr({ ...e._def, shape: () => t });
    } else
      return e instanceof ks
        ? new ks({ ...e._def, type: _s(e.element) })
        : e instanceof yr
          ? yr.create(_s(e.unwrap()))
          : e instanceof Rn
            ? Rn.create(_s(e.unwrap()))
            : e instanceof jn
              ? jn.create(e.items.map((t) => _s(t)))
              : e;
  }
  let hr = class Kt extends X {
    constructor() {
      (super(...arguments),
        (this._cached = null),
        (this.nonstrict = this.passthrough),
        (this.augment = this.extend));
    }
    _getCached() {
      if (this._cached !== null) return this._cached;
      const t = this._def.shape(),
        r = se.objectKeys(t);
      return ((this._cached = { shape: t, keys: r }), this._cached);
    }
    _parse(t) {
      if (this._getType(t) !== z.object) {
        const u = this._getOrReturnCtx(t);
        return (
          L(u, {
            code: P.invalid_type,
            expected: z.object,
            received: u.parsedType,
          }),
          V
        );
      }
      const { status: n, ctx: s } = this._processInputParams(t),
        { shape: a, keys: o } = this._getCached(),
        l = [];
      if (
        !(this._def.catchall instanceof Wr && this._def.unknownKeys === "strip")
      )
        for (const u in s.data) o.includes(u) || l.push(u);
      const c = [];
      for (const u of o) {
        const d = a[u],
          f = s.data[u];
        c.push({
          key: { status: "valid", value: u },
          value: d._parse(new Xt(s, f, s.path, u)),
          alwaysSet: u in s.data,
        });
      }
      if (this._def.catchall instanceof Wr) {
        const u = this._def.unknownKeys;
        if (u === "passthrough")
          for (const d of l)
            c.push({
              key: { status: "valid", value: d },
              value: { status: "valid", value: s.data[d] },
            });
        else if (u === "strict")
          l.length > 0 &&
            (L(s, { code: P.unrecognized_keys, keys: l }), n.dirty());
        else if (u !== "strip")
          throw new Error(
            "Internal ZodObject error: invalid unknownKeys value."
          );
      } else {
        const u = this._def.catchall;
        for (const d of l) {
          const f = s.data[d];
          c.push({
            key: { status: "valid", value: d },
            value: u._parse(new Xt(s, f, s.path, d)),
            alwaysSet: d in s.data,
          });
        }
      }
      return s.common.async
        ? Promise.resolve()
            .then(async () => {
              const u = [];
              for (const d of c) {
                const f = await d.key,
                  m = await d.value;
                u.push({ key: f, value: m, alwaysSet: d.alwaysSet });
              }
              return u;
            })
            .then((u) => at.mergeObjectSync(n, u))
        : at.mergeObjectSync(n, c);
    }
    get shape() {
      return this._def.shape();
    }
    strict(t) {
      return (
        F.errToObj,
        new Kt({
          ...this._def,
          unknownKeys: "strict",
          ...(t !== void 0
            ? {
                errorMap: (r, n) => {
                  const s =
                    this._def.errorMap?.(r, n).message ?? n.defaultError;
                  return r.code === "unrecognized_keys"
                    ? { message: F.errToObj(t).message ?? s }
                    : { message: s };
                },
              }
            : {}),
        })
      );
    }
    strip() {
      return new Kt({ ...this._def, unknownKeys: "strip" });
    }
    passthrough() {
      return new Kt({ ...this._def, unknownKeys: "passthrough" });
    }
    extend(t) {
      return new Kt({
        ...this._def,
        shape: () => ({ ...this._def.shape(), ...t }),
      });
    }
    merge(t) {
      return new Kt({
        unknownKeys: t._def.unknownKeys,
        catchall: t._def.catchall,
        shape: () => ({ ...this._def.shape(), ...t._def.shape() }),
        typeName: K.ZodObject,
      });
    }
    setKey(t, r) {
      return this.augment({ [t]: r });
    }
    catchall(t) {
      return new Kt({ ...this._def, catchall: t });
    }
    pick(t) {
      const r = {};
      for (const n of se.objectKeys(t))
        t[n] && this.shape[n] && (r[n] = this.shape[n]);
      return new Kt({ ...this._def, shape: () => r });
    }
    omit(t) {
      const r = {};
      for (const n of se.objectKeys(this.shape)) t[n] || (r[n] = this.shape[n]);
      return new Kt({ ...this._def, shape: () => r });
    }
    deepPartial() {
      return _s(this);
    }
    partial(t) {
      const r = {};
      for (const n of se.objectKeys(this.shape)) {
        const s = this.shape[n];
        t && !t[n] ? (r[n] = s) : (r[n] = s.optional());
      }
      return new Kt({ ...this._def, shape: () => r });
    }
    required(t) {
      const r = {};
      for (const n of se.objectKeys(this.shape))
        if (t && !t[n]) r[n] = this.shape[n];
        else {
          let a = this.shape[n];
          for (; a instanceof yr; ) a = a._def.innerType;
          r[n] = a;
        }
      return new Kt({ ...this._def, shape: () => r });
    }
    keyof() {
      return xh(se.objectKeys(this.shape));
    }
  };
  ((hr.create = (e, t) =>
    new hr({
      shape: () => e,
      unknownKeys: "strip",
      catchall: Wr.create(),
      typeName: K.ZodObject,
      ...H(t),
    })),
    (hr.strictCreate = (e, t) =>
      new hr({
        shape: () => e,
        unknownKeys: "strict",
        catchall: Wr.create(),
        typeName: K.ZodObject,
        ...H(t),
      })),
    (hr.lazycreate = (e, t) =>
      new hr({
        shape: e,
        unknownKeys: "strip",
        catchall: Wr.create(),
        typeName: K.ZodObject,
        ...H(t),
      })));
  let ki = class extends X {
    _parse(t) {
      const { ctx: r } = this._processInputParams(t),
        n = this._def.options;
      function s(a) {
        for (const l of a) if (l.result.status === "valid") return l.result;
        for (const l of a)
          if (l.result.status === "dirty")
            return (r.common.issues.push(...l.ctx.common.issues), l.result);
        const o = a.map((l) => new Jt(l.ctx.common.issues));
        return (L(r, { code: P.invalid_union, unionErrors: o }), V);
      }
      if (r.common.async)
        return Promise.all(
          n.map(async (a) => {
            const o = {
              ...r,
              common: { ...r.common, issues: [] },
              parent: null,
            };
            return {
              result: await a._parseAsync({
                data: r.data,
                path: r.path,
                parent: o,
              }),
              ctx: o,
            };
          })
        ).then(s);
      {
        let a;
        const o = [];
        for (const c of n) {
          const u = { ...r, common: { ...r.common, issues: [] }, parent: null },
            d = c._parseSync({ data: r.data, path: r.path, parent: u });
          if (d.status === "valid") return d;
          (d.status === "dirty" && !a && (a = { result: d, ctx: u }),
            u.common.issues.length && o.push(u.common.issues));
        }
        if (a) return (r.common.issues.push(...a.ctx.common.issues), a.result);
        const l = o.map((c) => new Jt(c));
        return (L(r, { code: P.invalid_union, unionErrors: l }), V);
      }
    }
    get options() {
      return this._def.options;
    }
  };
  ki.create = (e, t) => new ki({ options: e, typeName: K.ZodUnion, ...H(t) });
  const gr = (e) =>
    e instanceof ju
      ? gr(e.schema)
      : e instanceof Nn
        ? gr(e.innerType())
        : e instanceof Si
          ? [e.value]
          : e instanceof Ci
            ? e.options
            : e instanceof Nu
              ? se.objectValues(e.enum)
              : e instanceof Ni
                ? gr(e._def.innerType)
                : e instanceof wu
                  ? [void 0]
                  : e instanceof ku
                    ? [null]
                    : e instanceof yr
                      ? [void 0, ...gr(e.unwrap())]
                      : e instanceof Rn
                        ? [null, ...gr(e.unwrap())]
                        : e instanceof wh || e instanceof Ai
                          ? gr(e.unwrap())
                          : e instanceof Ri
                            ? gr(e._def.innerType)
                            : [];
  class Su extends X {
    _parse(t) {
      const { ctx: r } = this._processInputParams(t);
      if (r.parsedType !== z.object)
        return (
          L(r, {
            code: P.invalid_type,
            expected: z.object,
            received: r.parsedType,
          }),
          V
        );
      const n = this.discriminator,
        s = r.data[n],
        a = this.optionsMap.get(s);
      return a
        ? r.common.async
          ? a._parseAsync({ data: r.data, path: r.path, parent: r })
          : a._parseSync({ data: r.data, path: r.path, parent: r })
        : (L(r, {
            code: P.invalid_union_discriminator,
            options: Array.from(this.optionsMap.keys()),
            path: [n],
          }),
          V);
    }
    get discriminator() {
      return this._def.discriminator;
    }
    get options() {
      return this._def.options;
    }
    get optionsMap() {
      return this._def.optionsMap;
    }
    static create(t, r, n) {
      const s = new Map();
      for (const a of r) {
        const o = gr(a.shape[t]);
        if (!o.length)
          throw new Error(
            `A discriminator value for key \`${t}\` could not be extracted from all schema options`
          );
        for (const l of o) {
          if (s.has(l))
            throw new Error(
              `Discriminator property ${String(t)} has duplicate value ${String(l)}`
            );
          s.set(l, a);
        }
      }
      return new Su({
        typeName: K.ZodDiscriminatedUnion,
        discriminator: t,
        options: r,
        optionsMap: s,
        ...H(n),
      });
    }
  }
  function Cu(e, t) {
    const r = Kr(e),
      n = Kr(t);
    if (e === t) return { valid: !0, data: e };
    if (r === z.object && n === z.object) {
      const s = se.objectKeys(t),
        a = se.objectKeys(e).filter((l) => s.indexOf(l) !== -1),
        o = { ...e, ...t };
      for (const l of a) {
        const c = Cu(e[l], t[l]);
        if (!c.valid) return { valid: !1 };
        o[l] = c.data;
      }
      return { valid: !0, data: o };
    } else if (r === z.array && n === z.array) {
      if (e.length !== t.length) return { valid: !1 };
      const s = [];
      for (let a = 0; a < e.length; a++) {
        const o = e[a],
          l = t[a],
          c = Cu(o, l);
        if (!c.valid) return { valid: !1 };
        s.push(c.data);
      }
      return { valid: !0, data: s };
    } else
      return r === z.date && n === z.date && +e == +t
        ? { valid: !0, data: e }
        : { valid: !1 };
  }
  let _i = class extends X {
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t),
        s = (a, o) => {
          if (uh(a) || uh(o)) return V;
          const l = Cu(a.value, o.value);
          return l.valid
            ? ((dh(a) || dh(o)) && r.dirty(),
              { status: r.value, value: l.data })
            : (L(n, { code: P.invalid_intersection_types }), V);
        };
      return n.common.async
        ? Promise.all([
            this._def.left._parseAsync({
              data: n.data,
              path: n.path,
              parent: n,
            }),
            this._def.right._parseAsync({
              data: n.data,
              path: n.path,
              parent: n,
            }),
          ]).then(([a, o]) => s(a, o))
        : s(
            this._def.left._parseSync({
              data: n.data,
              path: n.path,
              parent: n,
            }),
            this._def.right._parseSync({
              data: n.data,
              path: n.path,
              parent: n,
            })
          );
    }
  };
  _i.create = (e, t, r) =>
    new _i({ left: e, right: t, typeName: K.ZodIntersection, ...H(r) });
  class jn extends X {
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t);
      if (n.parsedType !== z.array)
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.array,
            received: n.parsedType,
          }),
          V
        );
      if (n.data.length < this._def.items.length)
        return (
          L(n, {
            code: P.too_small,
            minimum: this._def.items.length,
            inclusive: !0,
            exact: !1,
            type: "array",
          }),
          V
        );
      !this._def.rest &&
        n.data.length > this._def.items.length &&
        (L(n, {
          code: P.too_big,
          maximum: this._def.items.length,
          inclusive: !0,
          exact: !1,
          type: "array",
        }),
        r.dirty());
      const a = [...n.data]
        .map((o, l) => {
          const c = this._def.items[l] || this._def.rest;
          return c ? c._parse(new Xt(n, o, n.path, l)) : null;
        })
        .filter((o) => !!o);
      return n.common.async
        ? Promise.all(a).then((o) => at.mergeArray(r, o))
        : at.mergeArray(r, a);
    }
    get items() {
      return this._def.items;
    }
    rest(t) {
      return new jn({ ...this._def, rest: t });
    }
  }
  jn.create = (e, t) => {
    if (!Array.isArray(e))
      throw new Error("You must pass an array of schemas to z.tuple([ ... ])");
    return new jn({ items: e, typeName: K.ZodTuple, rest: null, ...H(t) });
  };
  let sb = class yd extends X {
    get keySchema() {
      return this._def.keyType;
    }
    get valueSchema() {
      return this._def.valueType;
    }
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t);
      if (n.parsedType !== z.object)
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.object,
            received: n.parsedType,
          }),
          V
        );
      const s = [],
        a = this._def.keyType,
        o = this._def.valueType;
      for (const l in n.data)
        s.push({
          key: a._parse(new Xt(n, l, n.path, l)),
          value: o._parse(new Xt(n, n.data[l], n.path, l)),
          alwaysSet: l in n.data,
        });
      return n.common.async
        ? at.mergeObjectAsync(r, s)
        : at.mergeObjectSync(r, s);
    }
    get element() {
      return this._def.valueType;
    }
    static create(t, r, n) {
      return r instanceof X
        ? new yd({ keyType: t, valueType: r, typeName: K.ZodRecord, ...H(n) })
        : new yd({
            keyType: bi.create(),
            valueType: t,
            typeName: K.ZodRecord,
            ...H(r),
          });
    }
  };
  class vh extends X {
    get keySchema() {
      return this._def.keyType;
    }
    get valueSchema() {
      return this._def.valueType;
    }
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t);
      if (n.parsedType !== z.map)
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.map,
            received: n.parsedType,
          }),
          V
        );
      const s = this._def.keyType,
        a = this._def.valueType,
        o = [...n.data.entries()].map(([l, c], u) => ({
          key: s._parse(new Xt(n, l, n.path, [u, "key"])),
          value: a._parse(new Xt(n, c, n.path, [u, "value"])),
        }));
      if (n.common.async) {
        const l = new Map();
        return Promise.resolve().then(async () => {
          for (const c of o) {
            const u = await c.key,
              d = await c.value;
            if (u.status === "aborted" || d.status === "aborted") return V;
            ((u.status === "dirty" || d.status === "dirty") && r.dirty(),
              l.set(u.value, d.value));
          }
          return { status: r.value, value: l };
        });
      } else {
        const l = new Map();
        for (const c of o) {
          const u = c.key,
            d = c.value;
          if (u.status === "aborted" || d.status === "aborted") return V;
          ((u.status === "dirty" || d.status === "dirty") && r.dirty(),
            l.set(u.value, d.value));
        }
        return { status: r.value, value: l };
      }
    }
  }
  vh.create = (e, t, r) =>
    new vh({ valueType: t, keyType: e, typeName: K.ZodMap, ...H(r) });
  class Ma extends X {
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t);
      if (n.parsedType !== z.set)
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.set,
            received: n.parsedType,
          }),
          V
        );
      const s = this._def;
      (s.minSize !== null &&
        n.data.size < s.minSize.value &&
        (L(n, {
          code: P.too_small,
          minimum: s.minSize.value,
          type: "set",
          inclusive: !0,
          exact: !1,
          message: s.minSize.message,
        }),
        r.dirty()),
        s.maxSize !== null &&
          n.data.size > s.maxSize.value &&
          (L(n, {
            code: P.too_big,
            maximum: s.maxSize.value,
            type: "set",
            inclusive: !0,
            exact: !1,
            message: s.maxSize.message,
          }),
          r.dirty()));
      const a = this._def.valueType;
      function o(c) {
        const u = new Set();
        for (const d of c) {
          if (d.status === "aborted") return V;
          (d.status === "dirty" && r.dirty(), u.add(d.value));
        }
        return { status: r.value, value: u };
      }
      const l = [...n.data.values()].map((c, u) =>
        a._parse(new Xt(n, c, n.path, u))
      );
      return n.common.async ? Promise.all(l).then((c) => o(c)) : o(l);
    }
    min(t, r) {
      return new Ma({
        ...this._def,
        minSize: { value: t, message: F.toString(r) },
      });
    }
    max(t, r) {
      return new Ma({
        ...this._def,
        maxSize: { value: t, message: F.toString(r) },
      });
    }
    size(t, r) {
      return this.min(t, r).max(t, r);
    }
    nonempty(t) {
      return this.min(1, t);
    }
  }
  Ma.create = (e, t) =>
    new Ma({
      valueType: e,
      minSize: null,
      maxSize: null,
      typeName: K.ZodSet,
      ...H(t),
    });
  class ju extends X {
    get schema() {
      return this._def.getter();
    }
    _parse(t) {
      const { ctx: r } = this._processInputParams(t);
      return this._def
        .getter()
        ._parse({ data: r.data, path: r.path, parent: r });
    }
  }
  ju.create = (e, t) => new ju({ getter: e, typeName: K.ZodLazy, ...H(t) });
  class Si extends X {
    _parse(t) {
      if (t.data !== this._def.value) {
        const r = this._getOrReturnCtx(t);
        return (
          L(r, {
            received: r.data,
            code: P.invalid_literal,
            expected: this._def.value,
          }),
          V
        );
      }
      return { status: "valid", value: t.data };
    }
    get value() {
      return this._def.value;
    }
  }
  Si.create = (e, t) => new Si({ value: e, typeName: K.ZodLiteral, ...H(t) });
  function xh(e, t) {
    return new Ci({ values: e, typeName: K.ZodEnum, ...H(t) });
  }
  let Ci = class vd extends X {
    _parse(t) {
      if (typeof t.data != "string") {
        const r = this._getOrReturnCtx(t),
          n = this._def.values;
        return (
          L(r, {
            expected: se.joinValues(n),
            received: r.parsedType,
            code: P.invalid_type,
          }),
          V
        );
      }
      if (
        (this._cache || (this._cache = new Set(this._def.values)),
        !this._cache.has(t.data))
      ) {
        const r = this._getOrReturnCtx(t),
          n = this._def.values;
        return (
          L(r, { received: r.data, code: P.invalid_enum_value, options: n }),
          V
        );
      }
      return Ot(t.data);
    }
    get options() {
      return this._def.values;
    }
    get enum() {
      const t = {};
      for (const r of this._def.values) t[r] = r;
      return t;
    }
    get Values() {
      const t = {};
      for (const r of this._def.values) t[r] = r;
      return t;
    }
    get Enum() {
      const t = {};
      for (const r of this._def.values) t[r] = r;
      return t;
    }
    extract(t, r = this._def) {
      return vd.create(t, { ...this._def, ...r });
    }
    exclude(t, r = this._def) {
      return vd.create(
        this.options.filter((n) => !t.includes(n)),
        { ...this._def, ...r }
      );
    }
  };
  Ci.create = xh;
  class Nu extends X {
    _parse(t) {
      const r = se.getValidEnumValues(this._def.values),
        n = this._getOrReturnCtx(t);
      if (n.parsedType !== z.string && n.parsedType !== z.number) {
        const s = se.objectValues(r);
        return (
          L(n, {
            expected: se.joinValues(s),
            received: n.parsedType,
            code: P.invalid_type,
          }),
          V
        );
      }
      if (
        (this._cache ||
          (this._cache = new Set(se.getValidEnumValues(this._def.values))),
        !this._cache.has(t.data))
      ) {
        const s = se.objectValues(r);
        return (
          L(n, { received: n.data, code: P.invalid_enum_value, options: s }),
          V
        );
      }
      return Ot(t.data);
    }
    get enum() {
      return this._def.values;
    }
  }
  Nu.create = (e, t) =>
    new Nu({ values: e, typeName: K.ZodNativeEnum, ...H(t) });
  class ji extends X {
    unwrap() {
      return this._def.type;
    }
    _parse(t) {
      const { ctx: r } = this._processInputParams(t);
      if (r.parsedType !== z.promise && r.common.async === !1)
        return (
          L(r, {
            code: P.invalid_type,
            expected: z.promise,
            received: r.parsedType,
          }),
          V
        );
      const n = r.parsedType === z.promise ? r.data : Promise.resolve(r.data);
      return Ot(
        n.then((s) =>
          this._def.type.parseAsync(s, {
            path: r.path,
            errorMap: r.common.contextualErrorMap,
          })
        )
      );
    }
  }
  ji.create = (e, t) => new ji({ type: e, typeName: K.ZodPromise, ...H(t) });
  class Nn extends X {
    innerType() {
      return this._def.schema;
    }
    sourceType() {
      return this._def.schema._def.typeName === K.ZodEffects
        ? this._def.schema.sourceType()
        : this._def.schema;
    }
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t),
        s = this._def.effect || null,
        a = {
          addIssue: (o) => {
            (L(n, o), o.fatal ? r.abort() : r.dirty());
          },
          get path() {
            return n.path;
          },
        };
      if (((a.addIssue = a.addIssue.bind(a)), s.type === "preprocess")) {
        const o = s.transform(n.data, a);
        if (n.common.async)
          return Promise.resolve(o).then(async (l) => {
            if (r.value === "aborted") return V;
            const c = await this._def.schema._parseAsync({
              data: l,
              path: n.path,
              parent: n,
            });
            return c.status === "aborted"
              ? V
              : c.status === "dirty" || r.value === "dirty"
                ? Da(c.value)
                : c;
          });
        {
          if (r.value === "aborted") return V;
          const l = this._def.schema._parseSync({
            data: o,
            path: n.path,
            parent: n,
          });
          return l.status === "aborted"
            ? V
            : l.status === "dirty" || r.value === "dirty"
              ? Da(l.value)
              : l;
        }
      }
      if (s.type === "refinement") {
        const o = (l) => {
          const c = s.refinement(l, a);
          if (n.common.async) return Promise.resolve(c);
          if (c instanceof Promise)
            throw new Error(
              "Async refinement encountered during synchronous parse operation. Use .parseAsync instead."
            );
          return l;
        };
        if (n.common.async === !1) {
          const l = this._def.schema._parseSync({
            data: n.data,
            path: n.path,
            parent: n,
          });
          return l.status === "aborted"
            ? V
            : (l.status === "dirty" && r.dirty(),
              o(l.value),
              { status: r.value, value: l.value });
        } else
          return this._def.schema
            ._parseAsync({ data: n.data, path: n.path, parent: n })
            .then((l) =>
              l.status === "aborted"
                ? V
                : (l.status === "dirty" && r.dirty(),
                  o(l.value).then(() => ({ status: r.value, value: l.value })))
            );
      }
      if (s.type === "transform")
        if (n.common.async === !1) {
          const o = this._def.schema._parseSync({
            data: n.data,
            path: n.path,
            parent: n,
          });
          if (!ws(o)) return V;
          const l = s.transform(o.value, a);
          if (l instanceof Promise)
            throw new Error(
              "Asynchronous transform encountered during synchronous parse operation. Use .parseAsync instead."
            );
          return { status: r.value, value: l };
        } else
          return this._def.schema
            ._parseAsync({ data: n.data, path: n.path, parent: n })
            .then((o) =>
              ws(o)
                ? Promise.resolve(s.transform(o.value, a)).then((l) => ({
                    status: r.value,
                    value: l,
                  }))
                : V
            );
      se.assertNever(s);
    }
  }
  ((Nn.create = (e, t, r) =>
    new Nn({ schema: e, typeName: K.ZodEffects, effect: t, ...H(r) })),
    (Nn.createWithPreprocess = (e, t, r) =>
      new Nn({
        schema: t,
        effect: { type: "preprocess", transform: e },
        typeName: K.ZodEffects,
        ...H(r),
      })));
  let yr = class extends X {
    _parse(t) {
      return this._getType(t) === z.undefined
        ? Ot(void 0)
        : this._def.innerType._parse(t);
    }
    unwrap() {
      return this._def.innerType;
    }
  };
  yr.create = (e, t) =>
    new yr({ innerType: e, typeName: K.ZodOptional, ...H(t) });
  let Rn = class extends X {
    _parse(t) {
      return this._getType(t) === z.null
        ? Ot(null)
        : this._def.innerType._parse(t);
    }
    unwrap() {
      return this._def.innerType;
    }
  };
  Rn.create = (e, t) =>
    new Rn({ innerType: e, typeName: K.ZodNullable, ...H(t) });
  let Ni = class extends X {
    _parse(t) {
      const { ctx: r } = this._processInputParams(t);
      let n = r.data;
      return (
        r.parsedType === z.undefined && (n = this._def.defaultValue()),
        this._def.innerType._parse({ data: n, path: r.path, parent: r })
      );
    }
    removeDefault() {
      return this._def.innerType;
    }
  };
  Ni.create = (e, t) =>
    new Ni({
      innerType: e,
      typeName: K.ZodDefault,
      defaultValue:
        typeof t.default == "function" ? t.default : () => t.default,
      ...H(t),
    });
  let Ri = class extends X {
    _parse(t) {
      const { ctx: r } = this._processInputParams(t),
        n = { ...r, common: { ...r.common, issues: [] } },
        s = this._def.innerType._parse({
          data: n.data,
          path: n.path,
          parent: { ...n },
        });
      return xi(s)
        ? s.then((a) => ({
            status: "valid",
            value:
              a.status === "valid"
                ? a.value
                : this._def.catchValue({
                    get error() {
                      return new Jt(n.common.issues);
                    },
                    input: n.data,
                  }),
          }))
        : {
            status: "valid",
            value:
              s.status === "valid"
                ? s.value
                : this._def.catchValue({
                    get error() {
                      return new Jt(n.common.issues);
                    },
                    input: n.data,
                  }),
          };
    }
    removeCatch() {
      return this._def.innerType;
    }
  };
  Ri.create = (e, t) =>
    new Ri({
      innerType: e,
      typeName: K.ZodCatch,
      catchValue: typeof t.catch == "function" ? t.catch : () => t.catch,
      ...H(t),
    });
  class bh extends X {
    _parse(t) {
      if (this._getType(t) !== z.nan) {
        const n = this._getOrReturnCtx(t);
        return (
          L(n, {
            code: P.invalid_type,
            expected: z.nan,
            received: n.parsedType,
          }),
          V
        );
      }
      return { status: "valid", value: t.data };
    }
  }
  bh.create = (e) => new bh({ typeName: K.ZodNaN, ...H(e) });
  class wh extends X {
    _parse(t) {
      const { ctx: r } = this._processInputParams(t),
        n = r.data;
      return this._def.type._parse({ data: n, path: r.path, parent: r });
    }
    unwrap() {
      return this._def.type;
    }
  }
  class Ru extends X {
    _parse(t) {
      const { status: r, ctx: n } = this._processInputParams(t);
      if (n.common.async)
        return (async () => {
          const a = await this._def.in._parseAsync({
            data: n.data,
            path: n.path,
            parent: n,
          });
          return a.status === "aborted"
            ? V
            : a.status === "dirty"
              ? (r.dirty(), Da(a.value))
              : this._def.out._parseAsync({
                  data: a.value,
                  path: n.path,
                  parent: n,
                });
        })();
      {
        const s = this._def.in._parseSync({
          data: n.data,
          path: n.path,
          parent: n,
        });
        return s.status === "aborted"
          ? V
          : s.status === "dirty"
            ? (r.dirty(), { status: "dirty", value: s.value })
            : this._def.out._parseSync({
                data: s.value,
                path: n.path,
                parent: n,
              });
      }
    }
    static create(t, r) {
      return new Ru({ in: t, out: r, typeName: K.ZodPipeline });
    }
  }
  let Ai = class extends X {
    _parse(t) {
      const r = this._def.innerType._parse(t),
        n = (s) => (ws(s) && (s.value = Object.freeze(s.value)), s);
      return xi(r) ? r.then((s) => n(s)) : n(r);
    }
    unwrap() {
      return this._def.innerType;
    }
  };
  Ai.create = (e, t) =>
    new Ai({ innerType: e, typeName: K.ZodReadonly, ...H(t) });
  var K;
  (function (e) {
    ((e.ZodString = "ZodString"),
      (e.ZodNumber = "ZodNumber"),
      (e.ZodNaN = "ZodNaN"),
      (e.ZodBigInt = "ZodBigInt"),
      (e.ZodBoolean = "ZodBoolean"),
      (e.ZodDate = "ZodDate"),
      (e.ZodSymbol = "ZodSymbol"),
      (e.ZodUndefined = "ZodUndefined"),
      (e.ZodNull = "ZodNull"),
      (e.ZodAny = "ZodAny"),
      (e.ZodUnknown = "ZodUnknown"),
      (e.ZodNever = "ZodNever"),
      (e.ZodVoid = "ZodVoid"),
      (e.ZodArray = "ZodArray"),
      (e.ZodObject = "ZodObject"),
      (e.ZodUnion = "ZodUnion"),
      (e.ZodDiscriminatedUnion = "ZodDiscriminatedUnion"),
      (e.ZodIntersection = "ZodIntersection"),
      (e.ZodTuple = "ZodTuple"),
      (e.ZodRecord = "ZodRecord"),
      (e.ZodMap = "ZodMap"),
      (e.ZodSet = "ZodSet"),
      (e.ZodFunction = "ZodFunction"),
      (e.ZodLazy = "ZodLazy"),
      (e.ZodLiteral = "ZodLiteral"),
      (e.ZodEnum = "ZodEnum"),
      (e.ZodEffects = "ZodEffects"),
      (e.ZodNativeEnum = "ZodNativeEnum"),
      (e.ZodOptional = "ZodOptional"),
      (e.ZodNullable = "ZodNullable"),
      (e.ZodDefault = "ZodDefault"),
      (e.ZodCatch = "ZodCatch"),
      (e.ZodPromise = "ZodPromise"),
      (e.ZodBranded = "ZodBranded"),
      (e.ZodPipeline = "ZodPipeline"),
      (e.ZodReadonly = "ZodReadonly"));
  })(K || (K = {}));
  const k = bi.create,
    te = xu.create,
    $a = bu.create,
    kh = _u.create;
  Wr.create;
  const ve = ks.create,
    U = hr.create;
  ki.create;
  const Au = Su.create;
  (_i.create, jn.create);
  const Eu = sb.create,
    Ss = Si.create,
    Ne = Ci.create;
  (ji.create, yr.create, Rn.create);
  var _h = Ne(["ok", "action_required", "unavailable"]),
    Tu = U({
      id: k().min(1),
      title: k().min(1),
      severity: Ne(["info", "warning", "critical"]),
      actionUrl: k().optional(),
      deadline: k().datetime().optional(),
    }),
    Ei = U({
      status: _h,
      lastChecked: k().datetime(),
      actionsRequired: ve(Tu).default([]),
    });
  U({ gdpr: Ei, eidas: Ei, pci: Ei, schemaOrg: Ei });
  var ab = U({
      overallStatus: _h,
      pendingActionsCount: te().int().min(0),
      actions: ve(Tu).optional(),
    }),
    ob = U({
      value: te().int().min(0).max(100).nullable(),
      weight: te().min(0).max(1),
      recommendations: ve(k()).default([]),
      dataQuality: k().optional(),
    }).passthrough(),
    Sh = Eu(ob),
    ib = U({
      id: k().min(1),
      title: k().min(1),
      status: Ne(["todo", "done"]),
      actionUrl: k().url().optional(),
    }),
    lb = U({ steps: ve(ib) }),
    Ch = Ne([
      "cold_start",
      "human_established",
      "agent_verified_thin",
      "agent_verified",
      "insufficient_data",
    ]),
    jh = U({
      humanOrders: te().int().min(0),
      agentOrders: te().int().min(0),
      storeAgeDays: te().int().min(0),
      agenticEvidenceStatus: Ne([
        "verified",
        "not_yet_observed",
        "not_applicable",
      ]),
    }),
    Nh = U({
      status: Ss("ready"),
      score: te().int().min(0).max(100).nullable(),
      breakdown: Sh,
      computedAt: k().datetime(),
      expiresAt: k().datetime(),
      confidenceLevel: Ch,
      evidenceProfile: jh,
      scoreCap: te().int().min(0).max(100),
      nextMilestone: k().nullable(),
    }),
    Rh = U({ status: Ss("pending"), checklist: lb }),
    Ah = U({
      status: Ss("stale"),
      score: te().int().min(0).max(100).nullable(),
      breakdown: Sh,
      computedAt: k().datetime(),
      expiresAt: k().datetime(),
      confidenceLevel: Ch,
      evidenceProfile: jh,
      scoreCap: te().int().min(0).max(100),
      nextMilestone: k().nullable(),
    });
  Au("status", [Nh, Rh, Ah]);
  var Eh = Ne(["discovery", "customer", "checkout"]),
    Pu = U({
      id: k().min(1),
      callId: k().min(1),
      agentId: k().min(1),
      merchantId: k().min(1),
      bucket: Eh,
      tool: k().min(1),
      inputHash: k().regex(/^[a-f0-9]{64}$/, "expected SHA-256 hex"),
      outputHash: k().regex(/^[a-f0-9]{64}$/, "expected SHA-256 hex"),
      signingKeyKid: k().min(1),
      jws: k().min(1),
      jwksSnapshotUrl: k().url().nullish(),
      createdAt: k().datetime(),
    });
  U({
    agentId: k().optional(),
    bucket: Eh.optional(),
    from: k().datetime().optional(),
    to: k().datetime().optional(),
    cursor: k().optional(),
    limit: te().int().min(1).max(100).default(50),
  });
  var cb = U({ items: ve(Pu), nextCursor: k().nullable() });
  (U({
    agentId: k().optional(),
    from: k().datetime().optional(),
    to: k().datetime().optional(),
    format: Ne(["jws-zip", "json-bulk"]).default("jws-zip"),
  }),
    U({ jobId: k().min(1), status: Ss("queued") }));
  var ub = Ne(["pending", "active", "expired", "extended", "reverted"]);
  (U({ gracePeriodDays: te().int().min(1).max(90).default(30) }),
    U({
      id: k().min(1),
      merchantId: k().min(1),
      oldKeyKid: k().min(1),
      newKeyKid: k().min(1),
      status: ub,
      graceEndsAt: k().datetime(),
      notifiedAt: k().datetime().nullable(),
      createdAt: k().datetime(),
      updatedAt: k().datetime(),
    }));
  var Fa = U({
      activeKid: k().min(1),
      retiredGraceKids: ve(k().min(1)).default([]),
      lastRotatedAt: k().datetime().nullable(),
      daysSinceRotation: te().int().min(0).nullable(),
    }),
    Th = U({
      id: k().min(1),
      merchantId: k().min(1),
      agentId: k().nullable(),
      bucket: k().nullable(),
      tool: k().nullable(),
      source: Ne([
        "portal",
        "shopify-embed",
        "wp-embed",
        "api",
        "mcp",
        "webhook",
      ]),
      outcome: k().min(1),
      prevHash: k().nullable(),
      entryHash: k().min(1),
      metadata: Eu(kh()).default({}),
      createdAt: k().datetime(),
    });
  U({
    agentId: k().optional(),
    bucket: k().optional(),
    tool: k().optional(),
    source: Th.shape.source.optional(),
    from: k().datetime().optional(),
    to: k().datetime().optional(),
    cursor: k().optional(),
    limit: te().int().min(1).max(100).default(50),
  });
  var Ph = U({
      items: ve(Th),
      nextCursor: k().nullable(),
      chainIntegrity: U({
        verified: $a(),
        verifiedThrough: k().min(1).nullable(),
        note: k().optional(),
      }),
    }),
    db = U({
      catalog_completeness: te().min(0).max(1),
      catalog_freshness: te().min(0).max(1),
      price_accuracy: te().min(0).max(1),
      availability_accuracy: te().min(0).max(1),
      policy_coverage: te().min(0).max(1),
      checkout_success_rate: te().min(0).max(1),
      fulfillment_rate: te().min(0).max(1),
      dispute_rate: te().min(0).max(1),
      agent_satisfaction_rate: te().min(0).max(1),
      response_latency: te().min(0).max(1),
      review_sentiment: te().min(0).max(1),
      data_consistency: te().min(0).max(1),
    }),
    pb = U({
      fulfillmentOnTimeRate: te().min(0).max(1).nullable(),
      disputeRate: te().min(0).max(1).nullable(),
    }),
    Oh = U({
      score: Au("status", [Nh, Rh, Ah]),
      recentReceipts: ve(Pu).max(5),
      keysStatus: Fa.nullable(),
      complianceSummary: ab,
      components: db.nullable().optional(),
      componentsAreReal: $a().optional(),
      reliabilitySignals: pb.optional(),
      computedAt: k().datetime(),
    }),
    mb = Ne(["SHOPIFY", "WOO", "ACP"]);
  U({ platform: mb });
  var fb = U({ status: Ss("available"), receipt: Pu }),
    hb = U({
      status: Ss("not-found"),
      reason: Ne([
        "no_agent_attribution",
        "order_not_found",
        "receipt_not_yet_generated",
      ]),
    });
  Au("status", [fb, hb]);
  var Ih = Ne([
      "SHOPIFY",
      "WOO",
      "ACP",
      "MAGENTO",
      "PRESTASHOP",
      "ODOO",
      "WIX",
    ]),
    Lh = Ne([
      "created",
      "updated",
      "paid",
      "fulfilled",
      "cancelled",
      "refunded",
      "completed",
    ]),
    gb = U({
      amount: k().regex(/^-?\d+(\.\d+)?$/, "Decimal as string"),
      currency: k().length(3),
    }),
    Dh = U({
      id: k().min(1),
      platform: Ih,
      externalOrderId: k().min(1),
      status: Lh,
      total: gb,
      agentId: k().nullable(),
      paymentRail: k().nullable(),
      storeId: k().min(1),
      createdAt: k().datetime(),
      receiptUri: k().url().nullish(),
    }),
    yb = U({
      id: k().min(1),
      platformOrderId: k().min(1),
      type: k().min(1),
      payload: Eu(kh()).default({}),
      createdAt: k().datetime(),
    });
  (Dh.extend({ events: ve(yb).default([]) }),
    U({
      platform: Ih.optional(),
      status: Lh.optional(),
      agentId: k().optional(),
      from: k().datetime().optional(),
      to: k().datetime().optional(),
      search: k().optional(),
      cursor: k().optional(),
      limit: te().int().min(1).max(100).default(50),
    }));
  var zh = U({ items: ve(Dh), nextCursor: k().nullable() }),
    vb = Ne(["stripe-connect", "x402-wallet", "acp", "paypal", "eidas"]),
    Mh = Ne(["pending", "active", "error", "disabled"]),
    xb = U({
      id: k().min(1),
      type: vb,
      state: Mh,
      label: k().min(1),
      lastHealthCheck: k().datetime().nullable(),
      errorReason: k().nullable(),
      actions: ve(Ne(["rotate", "disable", "enable", "reconnect"])).default([]),
      createdAt: k().datetime(),
    });
  (U({ items: ve(xb) }),
    U({ reason: k().min(3).max(500).optional() }),
    U({ id: k().min(1), state: Mh, rotatedAt: k().datetime() }));
  var Ou = Ne(["allow", "block", "probation", "pending"]),
    bb = U({
      id: k().min(1),
      agentId: k().min(1),
      publicKey: k().min(1),
      displayName: k().nullable(),
      state: Ou,
      agenticTrustScore: te().int().min(0).max(100).nullable(),
      callsLast30d: te().int().min(0),
      lastCallAt: k().datetime().nullable(),
      createdAt: k().datetime(),
    });
  U({
    state: Ou.optional(),
    cursor: k().optional(),
    limit: te().int().min(1).max(100).default(50),
  });
  var wb = U({ items: ve(bb), nextCursor: k().nullable() }),
    kb = Ne(["revoke", "allow", "probation"]);
  U({ reason: k().min(3).max(500) });
  var _b = U({ id: k().min(1), state: Ou, updatedAt: k().datetime() });
  (U({
    items: ve(
      U({ agentId: k().min(1), action: kb, reason: k().min(3).max(500) })
    )
      .min(1)
      .max(50),
  }),
    U({
      succeeded: ve(k()),
      failed: ve(U({ agentId: k().min(1), error: k() })),
    }));
  var Iu = Ne(["stripe-connect", "x402", "acp", "paypal", "eidas"]),
    $h = U({
      rail: Iu,
      enabled: $a().default(!0),
      minAmount: k()
        .regex(/^-?\d+(\.\d+)?$/)
        .nullable()
        .default(null),
      maxAmount: k()
        .regex(/^-?\d+(\.\d+)?$/)
        .nullable()
        .default(null),
      currencyAllowlist: ve(k().length(3)).default([]),
    }),
    Fh = U({
      railsPriority: ve(Iu),
      rules: ve($h).default([]),
      updatedAt: k().datetime(),
    });
  U({ railsPriority: ve(Iu).min(1), rules: ve($h).optional() });
  var Sb = Ne([
      "active",
      "expiring_soon",
      "expired",
      "pending",
      "not_applicable",
    ]),
    Lu = U({
      status: Sb,
      expiresAt: k().datetime().nullable(),
      pendingSteps: ve(k()).default([]),
      actionsRequired: ve(Tu).default([]),
    }),
    Cb = U({ eidas: Lu, kyc: Lu, schemaOrg: Lu }),
    jb = Ne(["shopify-theme", "woocommerce-plugin", "custom-snippet"]),
    Nb = U({
      enabled: $a(),
      detectedAdapter: jb.nullable(),
      bridgeLastSeenAt: k().datetime().nullable(),
      updatedAt: k().datetime(),
    });
  (U({ enabled: $a() }),
    U({
      snippet: k().min(1),
      hash: k().regex(/^[a-f0-9]{64}$/),
      generatedAt: k().datetime(),
    }));
  var Rb = Ne(["idle", "indexing", "error", "never_indexed"]),
    Ab = U({
      status: Rb,
      lastIndexedAt: k().datetime().nullable(),
      productsIndexed: te().int().min(0),
      lastError: k().nullable(),
      jobId: k().nullable(),
    }),
    Eb = U({ jobId: k().min(1), enqueuedAt: k().datetime() }),
    Tb = U({
      ruleCode: k(),
      blockedCount: te().int().nonnegative(),
      failOpenCount: te().int().nonnegative(),
    });
  U({ window: k(), rules: ve(Tb) });
  var Bt = class extends Error {
      constructor(t, r) {
        super(t);
        qn(this, "status");
        qn(this, "body");
        qn(this, "url");
        ((this.name = "ApiClientError"),
          (this.status = r.status),
          (this.body = r.body),
          (this.url = r.url));
      }
    },
    Uh = class extends Bt {
      constructor(e) {
        (super(e.body?.error ?? e.body?.code ?? "auth_required", {
          status: 401,
          body: e.body,
          url: e.url,
        }),
          (this.name = "ApiAuthError"));
      }
    },
    Pb = class extends Bt {
      constructor(t) {
        super(t.body?.error ?? t.body?.code ?? "forbidden", {
          status: 403,
          body: t.body,
          url: t.url,
        });
        qn(this, "portalUpgradeUrl");
        this.name = "ApiForbiddenError";
        const r = t.body?.details?.portal_upgrade_url;
        this.portalUpgradeUrl = typeof r == "string" ? r : null;
      }
    },
    Zh = class extends Bt {
      constructor(t) {
        super(t.body?.message ?? "validation failed", {
          status: 422,
          body: t.body,
          url: t.url,
        });
        qn(this, "issues");
        ((this.name = "ApiValidationError"),
          (this.issues = t.issues ?? t.body?.issues ?? []));
      }
    },
    Ob = class extends Bt {
      constructor(t) {
        super(t.body?.error ?? "rate_limited", {
          status: 429,
          body: t.body,
          url: t.url,
        });
        qn(this, "retryAfterSeconds");
        ((this.name = "ApiRateLimitError"),
          (this.retryAfterSeconds = t.retryAfterSeconds));
      }
    },
    Ib = class extends Bt {
      constructor(e) {
        (super(e.body?.error ?? e.body?.message ?? "server_error", {
          status: e.status,
          body: e.body,
          url: e.url,
        }),
          (this.name = "ApiServerError"));
      }
    },
    Lb = (e) => {
      const { status: t, body: r, url: n, retryAfterHeader: s } = e;
      if (t === 401) return new Uh({ body: r, url: n });
      if (t === 403) return new Pb({ body: r, url: n });
      if (t === 422) return new Zh({ body: r, url: n });
      if (t === 429) {
        const a = s ? Number.parseInt(s, 10) : null;
        return new Ob({
          body: r,
          url: n,
          retryAfterSeconds: a !== null && Number.isFinite(a) ? a : null,
        });
      }
      return t >= 500
        ? new Ib({ status: t, body: r, url: n })
        : new Bt(r?.error ?? r?.message ?? `http_${t}`, {
            status: t,
            body: r,
            url: n,
          });
    },
    Db = async (e, t, r) => {
      const n = await Promise.resolve(e.getToken()),
        s = { Accept: "application/json", "X-Embed-Source": e.source };
      return (
        n && (s.Authorization = `Bearer ${n}`),
        r && (s["Idempotency-Key"] = r),
        t && (s["Content-Type"] = "application/json"),
        s
      );
    },
    zb = async (e) => {
      if (!(e.headers.get("content-type") ?? "").includes("application/json"))
        return { body: null, raw: null };
      try {
        const r = await e.json();
        return { body: r, raw: r };
      } catch {
        return { body: null, raw: null };
      }
    },
    Bh = (e) => {
      const t = e.baseUrl.replace(/\/+$/, ""),
        r = e.fetchImpl ?? ((a, o) => globalThis.fetch(a, o)),
        n = async (a, o, l, c, u, d) => {
          const f = c !== void 0;
          let m;
          u !== void 0
            ? ((m = { Accept: "application/json", "X-Embed-Source": e.source }),
              u && (m.Authorization = `Bearer ${u}`),
              d?.idempotencyKey && (m["Idempotency-Key"] = d.idempotencyKey),
              f && (m["Content-Type"] = "application/json"))
            : (m = await Db(e, f, d?.idempotencyKey));
          const v = {
            method: a,
            headers: m,
            ...(f ? { body: JSON.stringify(c) } : {}),
          };
          let x;
          if (d?.signal) v.signal = d.signal;
          else if (e.timeoutMs) {
            const p = new AbortController();
            ((v.signal = p.signal),
              (x = setTimeout(() => p.abort(), e.timeoutMs)));
          }
          let b;
          try {
            b = await r(o, v);
          } finally {
            x !== void 0 && clearTimeout(x);
          }
          const { raw: j, body: g } = await zb(b);
          if (!b.ok) {
            const p = b.headers.get("retry-after");
            throw Lb({
              status: b.status,
              body: g,
              url: o,
              retryAfterHeader: p,
            });
          }
          try {
            return l.parse(j);
          } catch (p) {
            throw p instanceof Jt
              ? new Zh({ body: g, url: o, issues: p.issues })
              : new Bt("response_schema_parse_failed", {
                  status: b.status,
                  body: g,
                  url: o,
                });
          }
        },
        s = async (a, o, l, c, u) => {
          const d = o.startsWith("http")
            ? o
            : `${t}${o.startsWith("/") ? o : `/${o}`}`;
          try {
            return await n(a, d, l, c, void 0, u);
          } catch (f) {
            if (f instanceof Uh && e.refreshTokenFn !== void 0) {
              const m = await e.refreshTokenFn();
              if (m !== null) return n(a, d, l, c, m, u);
            }
            throw f;
          }
        };
      return {
        get: (a, o, l) => s("GET", a, o, void 0, l),
        post: (a, o, l, c) => s("POST", a, l, o, c),
        put: (a, o, l, c) => s("PUT", a, l, o, c),
        del: (a, o, l) => s("DELETE", a, o, void 0, l),
      };
    };
  class Du extends Error {
    constructor(t, r) {
      (super(r ?? `Token refresh failed with HTTP ${t}`),
        (this.name = "TokenRefreshError"),
        (this.status = t));
    }
  }
  class Mb {
    constructor(t = {}) {
      $(this, ln, null);
      $(this, Un, null);
      $(this, Vt, null);
      $(this, Qa);
      $(this, Ga);
      $(this, Zi, 3e4);
      (I(this, Qa, t.fetchImpl ?? ((r, n) => globalThis.fetch(r, n))),
        I(this, Ga, t.expiryBufferMs ?? 3e4));
    }
    async getToken() {
      return h(this, ln) === null ||
        h(this, Un) === null ||
        Date.now() >= h(this, Un) - h(this, Ga)
        ? null
        : h(this, ln);
    }
    async refresh() {
      if (
        h(this, Vt) !== null &&
        h(this, Vt).status === 503 &&
        Date.now() - h(this, Vt).at < h(this, Zi)
      )
        throw new Du(h(this, Vt).status, h(this, Vt).message);
      const t = window.__AMCP_CONFIG__,
        r = `${t.restRoot}agenticmcps/v1/embed/token`,
        n = await h(this, Qa).call(this, r, {
          method: "POST",
          headers: { "X-WP-Nonce": t.nonce, Accept: "application/json" },
        });
      if (!n.ok) {
        let o,
          l = !1;
        try {
          const c = await n.json();
          typeof c.message == "string" &&
            c.message.length > 0 &&
            (o = c.message);
        } catch {
          l = !0;
        }
        throw (
          o === void 0 &&
            l &&
            (o = `El servidor de WordPress devolvió HTTP ${n.status} sin JSON. Comprueba que el sitio esté activo y que no esté en modo mantenimiento.`),
          I(this, Vt, { at: Date.now(), status: n.status, message: o }),
          new Du(n.status, o)
        );
      }
      I(this, Vt, null);
      const s = await n.json(),
        a = (s.expiresIn ?? 300) * 1e3;
      return (I(this, ln, s.token), I(this, Un, Date.now() + a), h(this, ln));
    }
    clear() {
      (I(this, ln, null), I(this, Un, null), I(this, Vt, null));
    }
  }
  ((ln = new WeakMap()),
    (Un = new WeakMap()),
    (Vt = new WeakMap()),
    (Qa = new WeakMap()),
    (Ga = new WeakMap()),
    (Zi = new WeakMap()));
  const Cs = new Mb(),
    $b = {},
    Fb = async () => {
      const e = await Cs.getToken();
      return e !== null ? e : Cs.refresh();
    },
    Ub = async () => {
      try {
        return await Cs.refresh();
      } catch {
        return (Cs.clear(), null);
      }
    },
    zu = (e) => {
      const t = $b.VITE_TRUSTEED_API_URL;
      if (t) return t;
      const r = window.__AMCP_CONFIG__;
      if (r?.apiBase) return r.apiBase;
      const n = window.__AMCP_PS_CONFIG__;
      if (n?.apiBase) return n.apiBase;
      throw new Error(
        "[SECURITY] VITE_TRUSTEED_API_URL or __AMCP_CONFIG__.apiBase is required in production builds. Set VITE_TRUSTEED_API_URL to https://api.trusteed.xyz."
      );
    };
  let qh = Bh({
      baseUrl: zu(),
      source: "wp-embed",
      getToken: Fb,
      refreshTokenFn: Ub,
    }),
    Vh = "wp-embed",
    Kh = zu();
  function Wh() {
    return Vh;
  }
  function Zb() {
    return Kh;
  }
  function Bb(e) {
    const t = e.baseUrl ?? zu();
    ((qh = Bh({ baseUrl: t, source: e.source, getToken: e.getToken })),
      (Vh = e.source),
      (Kh = t));
  }
  const ae = new Proxy(
      {},
      {
        get(e, t) {
          return qh[t];
        },
      }
    ),
    qb = {
      common: {
        loading: "Cargando...",
        error: "Error",
        errorUnknown: "Error desconocido",
        save: "Guardar",
        cancel: "Cancelar",
        close: "Cerrar",
        back: "Volver",
        retry: "Intentar de nuevo",
        active: "Activo",
        inactive: "Inactivo",
        enabled: "Habilitado",
        disabled: "Deshabilitado",
        noData: "Sin datos",
        notifications: "Notificaciones",
        closeNotification: "Cerrar notificación",
        loadingMore: "Cargando…",
        loadMore: "Cargar más",
        configSaved: "Configuración guardada correctamente.",
      },
      inicio: {
        title: "¿Cómo va mi tienda hoy?",
        errorLoadingSummary: "No se pudo cargar el resumen:",
        reputationSection: "Tu reputación como vendedor",
        scoreLabelComputing: "Calculando…",
        scoreLabelExcellent: "Excelente",
        scoreLabelAcceptable: "Aceptable",
        scoreLabelNeedsAttention: "Necesita atención",
        scoreDescComputing:
          "Trusteed está analizando tu tienda. Vuelve en unos minutos.",
        scoreDescExcellent:
          "Tu tienda tiene una reputación impecable entre los agentes de compra.",
        scoreDescAcceptable:
          "Tu tienda está bien, pero hay margen de mejora. Activa más reglas de seguridad.",
        scoreDescNeedsAttention:
          "Algunos agentes de compra tienen dudas sobre tu tienda. Revisa tus reglas.",
        scoreAriaLoading: "Cargando puntuación",
        updatedAt: "Actualizado el {{date}} UTC",
        scoreLow: "0 — Bajo",
        scoreMid: "50 — Medio",
        scoreHigh: "100 — Excelente",
        manageReputation: "Gestionar Reputación →",
        viewActivity: "Ver historial de actividad →",
        securitySection: "¿Está protegida tu tienda?",
        securityProtected: "Protegida",
        securityProtectedDesc:
          "Tu tienda tiene protección activa. Cada venta a un agente de compra queda registrada y firmada.",
        securityActiveKey: "Clave activa: {{kid}}",
        securityGraceKeys: "{{count}} en periodo de gracia",
        securityNotActive: "Sin activar",
        securityNotActiveDesc:
          "Tu tienda todavía no tiene el sello de confianza activado. Los agentes de compra no pueden ver que eres de fiar.",
        securityActivate: "Activar mi protección →",
        salesSection: "Últimas ventas a agentes de compra",
        salesEmpty: "Aún no tienes ventas a agentes de compra.",
        salesEmptyDesc:
          "Cuando un agente de compra compre en tu tienda, aparecerá aquí.",
        salesTableLabel: "Últimas ventas IA",
        salesColNumber: "Nº",
        salesColType: "Tipo",
        salesColBuyer: "Comprador",
        salesColDate: "Fecha",
        viewAllOrders: "Ver todos mis pedidos →",
        bucketCheckout: "Compra",
        bucketCustomer: "Consulta",
        bucketDiscovery: "Búsqueda",
      },
      trustCenter: {
        title: "Trust Center",
        tabOverview: "Resumen",
        tabReceipts: "Ventas IA",
        tabKeys: "Claves",
        tabAudit: "Auditoría",
        tabsAriaLabel: "Trust Center tabs",
        loading: "Cargando...",
      },
      trustReceipts: {
        panelTitle: "Panel de confianza",
        scoringMethodLink: "Cómo establecemos la puntuación de tu tienda",
        errorLoadingData:
          "Los datos de tu tienda se cargarán en cuanto el servicio esté disponible.",
        noData: "Sin datos.",
        scoreLow: "0 — Bajo",
        scoreMid: "50 — Medio",
        scoreCap: "Techo: {{cap}}/100",
        scoreHigh: "90–100 — Excelente",
        scoreCurrent: "Techo actual: {{cap}}/100",
        scoreComputing: "Calculando…",
        scoreExcellent: "Excelente",
        scoreImprovable: "Mejorable",
        scoreNeedsAttention: "Necesita atención",
        coldStartDesc:
          "Tu tienda acaba de empezar. Esta puntuación sube a medida que configuras los pasos básicos y recibes tus primeras ventas.",
        humanEstablishedDesc:
          "Tienes un buen historial como vendedor. Tu puntuación puede llegar hasta 85/100 antes de tu primera venta a un agente de compra.",
        scoreHighDesc:
          "Tu tienda tiene una reputación impecable. Los agentes confían plenamente en ti.",
        scoreMidDesc:
          "Tu tienda está bien valorada entre los agentes. Revisa las áreas en amarillo para seguir subiendo.",
        scoreLowDesc:
          "Algunos agentes de compra tienen dudas. Actúa sobre las áreas en rojo lo antes posible.",
        computedAt: "Calculado el {{date}} UTC",
        pendingActionsOne: "mejora pendiente",
        pendingActionsMany: "mejoras pendientes",
        breakdownTitle: "Desglose por área",
        coldStartNotice:
          "Tu tienda acaba de empezar. La puntuación de historial como vendedor y la experiencia con agentes estarán disponibles cuando tengas más actividad — es normal, no es negativo.",
        notApplicable: "No aplica aún",
        weightLabel: "peso {{pct}}%",
        noDataLabel: "Sin datos",
        countWillActivate: "Contará cuando se active:",
        measuredNow: "Lo que se mide en tu etapa actual",
        checklistTitle: "¿Qué hago para mejorar mi puntuación?",
        checklistProgress: "{{done}} de {{total}} completados",
        checklistMissingMany:
          "— te faltan {{count}} cosas para llegar al máximo",
        checklistMissingOne: "— te falta {{count}} cosa para llegar al máximo",
        checklistAllDone: "— ya tienes todo en orden para empezar",
        checklistNow: "Ahora mismo",
        checklistAutoTitle: "Con tus primeras ventas",
        checklistAutoNote:
          "Estos factores se miden solos cuando empiezan a llegar pedidos. No tienes que hacer nada — el sistema los recoge automáticamente.",
        checklistAutoActiveNote:
          "Estos factores ya se están midiendo con tus ventas reales y cuentan para tu puntuación.",
        practicesTitle: "Tres cosas que siempre funcionan",
        faqTitle: "Preguntas que se hace todo el mundo",
        securityTitle: "¿Está protegida tu tienda?",
        securityActiveDesc:
          "Cada venta a un agente de compra queda registrada y sellada.",
        securityProtectionActive: "Protección activa.",
        securityRotationWarning:
          "Hace más de {{days}} días que no se renueva la protección. Contacta con soporte si ves algo raro.",
        securityNotActive:
          "Tu tienda todavía no tiene el sello de confianza activado.",
        securityActivate: "Activar mi protección →",
        goToKeys: "Ir a Claves",
        milestoneText: {
          no_store: "Abre tu tienda para empezar a construir tu reputación.",
          cold_start:
            "Completa el onboarding y haz tus primeras ventas para subir tu puntuación.",
          human_established:
            "Haz tus primeras ventas a agentes de compra para desbloquear el nivel máximo.",
        },
        recText: {
          activate_security_key:
            "Activa tu sello de confianza (clave de firma).",
          add_return_policy: "Añade una política de devoluciones clara.",
          add_shipping_policy: "Añade un tiempo estimado de envío.",
          complete_first_agent_sale:
            "Completa tu primera venta a un agente de compra.",
          more_agent_sales: "Consigue más ventas a agentes de compra.",
          improve_fulfillment_rate: "Mejora tu tasa de envíos a tiempo.",
          reduce_disputes: "Reduce disputas y reembolsos.",
          external_assurance_unavailable:
            "La garantía externa aún no está disponible.",
        },
        checklistSello: "Sello de seguridad activo",
        checklistSelloDetail:
          "Garantiza que cada pedido de un agente de compra está firmado. Sin él, los agentes no confían en tu tienda.",
        checklistCompliance: "Sin errores en tus métodos de pago",
        checklistComplianceDetail:
          "Tienes un error en tu método de cobro. Los agentes de compra no pueden terminar sus compras.",
        checklistComplianceAction: "Abrir ajustes de pago en WooCommerce",
        checklistDevoluciones: "Política de devoluciones",
        checklistDevolucionesDetail:
          "¿Cuántos días tiene el cliente? ¿Pagas el envío de vuelta? Si no está escrito, los agentes lo cuentan en tu contra.",
        checklistDevolucionesAction: "Abrir página de políticas en WooCommerce",
        checklistEnvio: "Tiempo estimado de envío",
        checklistEnvioDetail:
          "Indica cuántos días tardas en enviar. Si no lo pones, parece que no tienes claro tu propio proceso.",
        checklistEnvioAction: "Abrir ajustes de envíos en WooCommerce",
        checklistCatalogo: "Catálogo con fotos, precios y descripciones",
        checklistCatalogoDetail:
          "Tus productos necesitan foto, precio real, stock actualizado y descripción. Esto ya cuenta desde el primer día.",
        checklistCatalogoAction: "Abrir listado de productos",
        checklistFulfillment: "Envíos a tiempo",
        checklistFulfillmentDetail:
          "Se mide sola — el % que llegan en el plazo prometido.",
        checklistDisputes: "Pocas disputas y reembolsos",
        checklistDisputesDetail:
          "Se mide sola — el % de pedidos con reclamación en los últimos 90 días.",
        storeAgeLine: "Tu tienda lleva",
        storeActive: "activa",
        storeYear: "año",
        storeYears: "años",
        storeDays: "días",
        practice1Title: "Mantén tu catálogo al día",
        practice1Body:
          "Actualiza el stock, los precios y las fotos para que los compradores automáticos vean lo mismo que tienes en la trastienda. Si lo que muestras no coincide con lo que tienes, la confianza baja.",
        practice2Title: "Pon tus normas de devolución por escrito",
        practice2Body:
          "¿Cuántos días tiene el cliente para devolver? ¿Pagas tú el envío? ¿Se devuelve el dinero o solo vale cambio? Si no lo tienes escrito, los compradores automáticos lo cuentan en tu contra.",
        practice3Title: "Envía rápido y sin sorpresas",
        practice3Body:
          "Cuando un pedido llega tarde o hay una reclamación, tu puntuación baja. No hace falta ser Amazon — solo cumplir lo que prometes. Si dices 3 días, envía en 3 días.",
        faq1Q:
          "¿Esta puntuación es lo mismo que las estrellas que me ponen los clientes?",
        faq1A:
          "No. Las estrellas te las pone la gente que compra. Esta puntuación la calcula Trusteed mirando cómo funciona tu tienda por dentro: si el stock cuadra, si los pedidos llegan, si hay reclamaciones. Es una cosa completamente distinta.",
        faq2Q: "¿Si tengo 100 puntos vendo más?",
        faq2A:
          "No directamente. Tener una puntuación alta hace que más compradores automáticos encuentren tu tienda y confíen en ella. Pero que compren o no depende de tus precios, tu catálogo y lo que vendas.",
        faq3Q: "¿Me pueden quitar visibilidad antes de cerrarme la tienda?",
        faq3A:
          "Sí. Si la puntuación baja mucho, primero apareces menos en las búsquedas. Si sigue bajando, la tienda se oculta. Y si no se arregla, se puede llegar a suspender. Por eso conviene actuar antes de que llegue a ese punto.",
        humanOrders: "pedido",
        humanOrdersPlural: "pedidos",
        humanOrdersOf: "de clientes humanos",
        agentSale: "venta",
        agentSalePlural: "ventas",
        agentSalesOf: "a agentes de compra",
        confidenceInsufficient:
          "Completa la configuración para ver tu puntuación",
        confidenceColdStart: "Sin historial suficiente",
        confidenceHumanEstablished: "Vendedor con historial humano",
        confidenceAgentThin: "Primeras ventas a agentes",
        confidenceAgentVerified: "Verificado con agentes",
        unlockAreaDefault:
          "Esta área se activará cuando tengas más actividad en tu tienda.",
        unlockMerchantColdStart:
          "Se activa cuando tengas al menos 5 ventas a clientes. Aún no tienes historial suficiente.",
        unlockAgentColdStart:
          "Se activa con tus primeras ventas a agentes de compra.",
        unlockAgentHuman:
          "Se activa con tus primeras ventas a agentes de compra.",
        dimMerchantReliability: "🏪 ¿Qué historial tienes como vendedor?",
        dimAgenticReadiness: "⚙️ ¿Está tu tienda lista para agentes de compra?",
        dimAgenticEvidence:
          "🤝 ¿Qué experiencia han tenido los agentes con tu tienda?",
        subDim: {
          setupQuality: "Catálogo, políticas y precios",
          protocolSecurity: "Sello de seguridad (firma digital)",
          complianceHealth: "Sin errores en tus métodos de pago",
          integrationFreshness: "Catálogo al día y sincronizado",
          fulfillmentPerformance: "Pedidos enviados en el plazo prometido",
          disputeAndRefundHealth: "Pocas reclamaciones o devoluciones",
          orderCompletionHealth: "Pedidos completados sin cancelar",
          catalogOperationalQuality: "Catálogo completo y actualizado",
          storeAgeConfidence: "Antigüedad de tu tienda",
          humanOrderVolumeConfidence: "Número de clientes atendidos",
          agentCheckoutSuccessRate: "Compras de agentes completadas",
          agentFulfillmentOnTimeRate:
            "Envíos a compradores automáticos a tiempo",
          agentDisputeRefundHealth: "Pocas disputas en ventas a agentes",
          receiptIntegrity: "Recibos digitales firmados",
          agentLatencyAndToolReliability: "Rapidez al responder a agentes",
          repeatAgentSuccess: "Agentes que vuelven a comprar",
          fulfillmentRate: "Pedidos enviados a tiempo",
          disputeRate: "Pocas quejas o devoluciones",
          returnWindowClarity: "Tienes escritas las normas de devolución",
          catalogCompleteness: "Productos con fotos, precios y stock",
          storeAgeBonus: "Cuánto tiempo lleva abierta tu tienda",
          hasActiveKey: "Clave de firma digital activa",
          hasShippingPolicy: "Tienes escrito cuánto tardas en enviar",
          hasReturnPolicy: "Tienes escritas las normas de devolución",
          paymentMethodsSetup: "Puedes recibir pagos",
          mcpEndpointHealthy: "Los agentes de compra pueden llegar a tu tienda",
          agentOrderSuccessRate: "Agentes de compra completan sus pedidos",
          trustReceiptCoverage: "Comprobantes firmados en cada venta a agente",
          agentConsentCompliance:
            "Los agentes pueden comprar sin inconvenientes",
        },
      },
      paymentMethods: {
        title: "Metodos de pago",
        ariaLoading: "Cargando metodos de pago",
        errorLoading: "No se pudieron cargar los metodos de pago:",
        noMethods: "No hay metodos de pago configurados.",
        listAriaLabel: "Lista de metodos de pago",
        rotateBtnLabel: "Rotar credenciales de {{label}}",
        rotateBtn: "Rotar credenciales",
        dialogTitle: "Rotar credenciales",
        dialogDesc:
          "Confirmas la rotacion de credenciales? Esta accion no se puede deshacer.",
        confirmBtn: "Confirmar",
        cancelBtn: "Cancelar",
        rotating: "Rotando...",
        rotateSuccess: "Credenciales rotadas correctamente",
        rotateErrorFallback: "Error al rotar credenciales",
        closeNotification: "Cerrar notificacion",
        statePending: "Pendiente",
        stateActive: "Activo",
        stateError: "Error",
        stateDisabled: "Deshabilitado",
      },
      misVentas: {
        title: "Mis ventas",
        tabOrders: "Mis pedidos",
        tabReceipts: "Ventas IA",
        tabKeys: "Claves",
        tabAudit: "Auditoría",
        tabsAriaLabel: "Secciones de Mis ventas",
        ordersSection: "Todos mis pedidos",
        ordersSectionDesc:
          "Aquí aparecen todos los pedidos que han entrado en tu tienda, tanto de compradores normales como de agentes de compra.",
        ordersErrorLoading: "No se pudieron cargar los pedidos:",
        ordersEmpty: "Aún no tienes pedidos registrados.",
        ordersEmptyDesc:
          "Cuando alguien compre en tu tienda, el pedido aparecerá aquí.",
        ordersTableLabel: "Lista de pedidos",
        colOrderNumber: "Nº pedido",
        colStore: "Tienda",
        colStatus: "Estado",
        colTotal: "Total",
        colBuyer: "Quién compró",
        colDate: "Fecha",
        platformShopify: "Shopify",
        platformWoo: "WooCommerce",
        platformAcp: "Otro canal",
        statusCreated: "Creada",
        statusUpdated: "Actualizada",
        statusPaid: "Pagada",
        statusFulfilled: "Completada",
        statusCancelled: "Cancelada",
        statusRefunded: "Reembolsada",
        buyerAuto: "🤖 Comprador automático #{{id}}",
        buyerHuman: "👤 Persona",
        loadMoreOrders: "Cargar más pedidos →",
        receiptsSection: "Comprobantes de ventas a agentes de compra",
        receiptsSectionDesc:
          "Cada vez que un agente de compra compra en tu tienda, se genera un comprobante firmado y sellado con hora.",
        receiptsBenefits:
          "✅ ¿Para qué sirve? Si un comprador dice 'yo no hice ese pedido', este comprobante — firmado, sellado con hora y a prueba de manipulación — es un respaldo sólido: puedes descargarlo y aportarlo a tu banco o abogado. Por sí solo no sustituye a la evidencia que te pidan en una disputa. Nosotros lo guardamos automáticamente el tiempo que exige la ley de tu país (entre 5 y 10 años), sin que hagas nada.",
        receiptsRegulation:
          "🏛️ Nivel técnico: estos comprobantes están diseñados siguiendo la norma europea de firma electrónica (eIDAS) y llevan firma digital + sello de tiempo. Aún no son firma ni sello cualificados (eso requiere una autoridad certificadora en la lista de confianza de la UE): valen como prueba de integridad verificable, no como evidencia de disputa lista para banco o tribunal.",
        receiptsErrorLoading: "No se pudieron cargar los comprobantes:",
        receiptsEmpty: "Aún no hay comprobantes de ventas automáticas.",
        receiptsEmptyDesc:
          "Cuando un agente de compra haga una compra, el comprobante aparecerá aquí automáticamente.",
        receiptsTableLabel: "Comprobantes IA",
        colReceiptNumber: "Nº comprobante",
        colType: "Tipo",
        colAction: "Qué hizo",
        colBuyer2: "Comprador",
        bucketCheckout: "Compra",
        bucketCustomer: "Consulta",
        bucketDiscovery: "Búsqueda",
      },
      misReglas: {
        title: "Mis Reglas",
        infoNotice:
          "Estas reglas se aplican automáticamente a cualquier agente de compra que intente comprar en tu tienda. Actívalas o desactívalas con un clic. Cuantas más actives, más protegida estará tu tienda.",
        errorNotice:
          "Las reglas se muestran en modo provisional. Los cambios se guardarán cuando el servicio esté disponible.",
        rulesByCategoryTitle: "Reglas por categoría",
        activeCountLabel: "{{active}} de {{total}} reglas activas",
        ruleActive: "Activa",
        ruleInactive: "Inactiva",
        blockedLast7d: "{{count}} bloqueados 7d",
        failOpenLast7d: "{{count}} fail-open 7d",
        toggleAriaActive: "Desactivar regla",
        toggleAriaInactive: "Activar regla",
        howItWorksTitle: "¿Cómo funcionan las reglas?",
        howInactive:
          "No se aplica. El agente de compra pasa sin ser evaluado por este criterio.",
        howInactiveLabel: "Regla inactiva:",
        howActive:
          "Se evalúa en cada compra. Si el agente de compra la incumple, se bloquea o se pone en revisión según la gravedad.",
        howActiveLabel: "Regla activa:",
        howStats:
          "Las insignias junto a cada regla muestran cuántos compradores han sido afectados en los últimos 7 días.",
        howStatsLabel: "Estadísticas en tiempo real:",
        ruleActivatedMsg: "Regla activada",
        ruleDeactivatedMsg: "Regla desactivada",
        ruleChangeError: "No se pudo cambiar la regla. Inténtalo de nuevo.",
        paramsTitle: "Parámetros",
        paramsSaveBtn: "Guardar parámetros",
        paramsResetBtn: "Descartar",
        paramsCsvHint: "Valores separados por comas",
        taxonomyUnavailable:
          "La búsqueda de categorías de Shopify no está disponible en esta plataforma",
        taxonomySearchPlaceholder: "Buscar categoría…",
        taxonomyRemoveLabel: "Quitar {{name}}",
        r001r009Redundancy:
          "R009 se ejecutará en modo sombra (mismo veredicto que R001 — gana la primera coincidencia). No cambiará resultados, solo separa la telemetría.",
        r002WithoutR001:
          "R001 está OFF, por lo que R002 solo bloquea tokens inválidos presentados. Activa R001 para exigir token en todas las solicitudes.",
        paramsRequiredHint:
          "Proporciona al menos uno de los parámetros listados antes de activar.",
        paramsRequiredError: "Falta parámetro requerido:",
        params: {
          "R001.requireAgentId": "Requerir ID del agente",
          "R001.merchantTier": "Nivel del comercio (high = exigir ID siempre)",
          "R004.maxKeyAgeHours": "Antigüedad máx. de la clave (horas)",
          "R009.requireAgentId": "Requerir ID del agente",
          "R010.minCompletedOrders": "Pedidos completados mínimos",
          "R003.allowedCategories": "Categorías permitidas (mandato)",
          "R012.categories": "Categorías bloqueadas",
          "option.alcohol": "Alcohol",
          "option.tobacco": "Tabaco",
          "option.firearms": "Armas de fuego",
          "option.pharmaceuticals": "Farmacéuticos",
          "option.adult": "Contenido adulto",
          "option.gambling": "Apuestas / Juego",
          "option.cbd": "CBD",
          "option.electronics_high_value": "Electrónica de alto valor",
          "option.jewelry": "Joyería",
          "option.crypto": "Cripto",
          "option.gift_cards": "Tarjetas regalo",
          "option.subscriptions": "Suscripciones",
          "R014.highRiskCountries": "Países de alto riesgo (ISO-2)",
          "R014.maxCancellations": "Cancelaciones máximas",
          "R014.windowDays": "Ventana (días)",
          "R015.maxDeltaBps": "Delta máx. de precio (bps)",
          "R016.minStock": "Stock mínimo",
          "R018.spikeMultiplier": "Multiplicador de pico",
          "R018.merchantAvgOrderCents": "Ticket medio del comercio (céntimos)",
          "R018.maxItemCount": "Ítems máx. por carrito",
          "R018.maxSingleSkuQty": "Cantidad máx. por SKU",
          "R021.minCompletedOrders":
            "Pedidos completados mín. para primera compra",
          "R022.allowedPaymentMethods": "Métodos de pago permitidos",
          "R022.blockedPaymentMethods": "Métodos de pago bloqueados",
          "option.card": "Tarjeta",
          "option.paypal": "PayPal",
          "option.bnpl": "Compra ahora, paga después (BNPL)",
          "option.klarna": "Klarna",
          "option.afterpay": "Afterpay",
          "option.bank_transfer": "Transferencia bancaria",
          "option.apple_pay": "Apple Pay",
          "option.google_pay": "Google Pay",
          "option.gift_card": "Tarjeta regalo",
          "R023.windowDays": "Ventana (días)",
          "R023.maxRatio": "Ratio máx. de reembolsos (0–1)",
          "R024.windowDays": "Ventana (días)",
          "R024.maxDisputes": "Disputas máximas",
          "R025.blockPoBox": "Bloquear apartados postales",
          "R025.blockFreightForwarder": "Bloquear reexpedidores",
          "R026.requireConsent": "Requerir consentimiento explícito",
          "R027.maxStoredValueCents": "Valor almacenado máx. (céntimos)",
          "R028.requirePurchaseOrder": "Requerir orden de compra",
          "R032.blockedCategoryIds": "Categorías bloqueadas",
          "R034.blockedSkus": "SKU bloqueados",
          "R035.maxCents": "Importe máx. del pedido (céntimos)",
          "R036.maxCentsPerLine": "Importe máx. por línea (céntimos)",
          "R038.maxQuantity": "Artículos máx. por pedido",
          "R039.maxPerSku": "Cantidad máx. por producto",
          "R043.ttlMinutes": "Validez de la aprobación (minutos)",
          "R048.blockedTypes": "Tipos digitales bloqueados",
          "option.license_key": "Clave de licencia",
          "option.downloadable": "Descargable",
          "option.stored_value": "Valor almacenado",
        },
        helpLinkLabel: "¿Qué hace esta regla? →",
        helpBase: "https://trusteed.xyz/es/agent-rules",
        categoryLabels: {
          identity: "Identidad",
          behavior: "Comportamiento",
          transaction: "Transacción",
          postsale: "Posventa",
          general: "General",
          other: "Otra",
        },
        rulesMeta: {
          "R001.verified-agent-required": {
            category: "identity",
            title: "El agente debe identificarse",
            description:
              "Si un robot o asistente quiere comprar en tu tienda, tiene que presentar su 'DNI digital'. Sin identificación, no hay compra.",
          },
          "R002.signature-spoof-block": {
            category: "identity",
            title: "Bloquear firma falsificada",
            description:
              "Si el robot intenta comprar con una firma digital que no cuadra, lo paramos de inmediato.",
          },
          "R003.mandate-boundary-match": {
            category: "identity",
            title:
              "El robot no puede gastar más de lo que le autoriza su dueño",
            description:
              "Si el asistente tiene un límite firmado de 100 € y pone 250 € en el carrito, el pedido se cancela.",
          },
          "R004.new-key-friction": {
            category: "identity",
            title: "Clave de identidad muy nueva",
            description:
              "Si el robot estrena clave de identidad, esperamos unas horas antes de dejarle comprar.",
          },
          "R005.revoked-agent-block": {
            category: "identity",
            title: "Robot con el acceso revocado",
            description:
              "Si el asistente está en la lista negra de Trusteed, no puede comprar en ninguna tienda.",
          },
          "R006.provider-confidence-tier": {
            category: "identity",
            title: "Bloquear compradores con mala reputación",
            description:
              "Si el proveedor de identidad del robot nos dice que su confianza es muy baja, no le dejo comprar.",
          },
          "R007.cross-merchant-abuse-signal": {
            category: "identity",
            title: "Robot que ya ha hecho daño en otras tiendas",
            description:
              "Si el asistente ha tenido problemas serios en otras tiendas de Trusteed, lo bloqueamos aquí también.",
          },
          "R008.scope-escalation-detection": {
            category: "identity",
            title: "El robot pide más permisos de los autorizados",
            description:
              "Si el asistente intenta acceder a zonas de tu tienda para las que no tiene permiso, lo paramos.",
          },
          "R009.agent-verification-required": {
            category: "behavior",
            title: "Verificación de agente obligatoria",
            description:
              "Para ciertas operaciones, el asistente debe demostrar su identidad antes de continuar.",
          },
          "R010.new-agent-probation": {
            category: "behavior",
            title: "Robot sin historial en tu tienda",
            description:
              "Si es la primera vez que este asistente compra aquí, añadimos una pequeña comprobación extra.",
          },
          "R011.repeat-failed-checkout": {
            category: "behavior",
            title: "Detectar compras sospechosamente rápidas",
            description:
              "Si el robot falla varias veces seguidas en el pago en pocos minutos, lo pausamos.",
          },
          "R012.high-risk-category": {
            category: "behavior",
            title: "Categoría de producto de riesgo alto",
            description:
              "Algunos productos (tarjetas regalo, vino, tabaco…) tienen más posibilidad de fraude. Añadimos precaución.",
          },
          "R013.return-policy-guard": {
            category: "behavior",
            title: "El robot no acepta tu política de devoluciones",
            description:
              "Si el asistente no ha aceptado las condiciones de devolución de tu tienda, el pedido se para.",
          },
          "R014.delivery-risk-guard": {
            category: "behavior",
            title: "País de envío de riesgo alto",
            description:
              "Si el destino del paquete está en una lista de países problemáticos (sanciones, fraude frecuente), lo revisamos.",
          },
          "R015.price-change-guard": {
            category: "transaction",
            title: "El precio cambió mucho durante el proceso",
            description:
              "Si el precio de un artículo sube o baja más de un % configurado entre que se pone en el carrito y se paga, lo paramos.",
          },
          "R016.stock-confidence-guard": {
            category: "transaction",
            title: "Stock insuficiente para el robot",
            description:
              "Si un producto está a punto de agotarse, solo dejamos que los humanos completen la compra.",
          },
          "R017.coupon-discount-anomaly": {
            category: "transaction",
            title: "Detectar búsqueda repetida de descuentos",
            description:
              "Si el robot prueba muchos cupones de descuento de forma anormal, lo marcamos como sospechoso.",
          },
          "R018.cart-composition-guard": {
            category: "transaction",
            title: "Detectar pedidos grandes o inusuales",
            description:
              "Si el importe del carrito es muy superior al pedido medio de tu tienda, lo revisamos antes de aprobar.",
          },
          "R019.country-jurisdiction": {
            category: "transaction",
            title: "País no permitido en tu tienda",
            description:
              "Si el robot intenta facturar desde un país fuera de tu lista de países autorizados, el pedido se cancela.",
          },
          "R020.business-hours": {
            category: "transaction",
            title: "Compras solo en horario comercial",
            description:
              "Si configuras un horario de atención, los robots no podrán completar pedidos fuera de ese horario.",
          },
          "R021.first-purchase-with-merchant": {
            category: "postsale",
            title: "Primera compra del robot en tu tienda",
            description:
              "Si nunca ha comprado aquí antes, añadimos una revisión extra aunque el robot sea conocido en otras tiendas.",
          },
          "R022.payment-rail-restriction": {
            category: "postsale",
            title: "Método de pago no permitido",
            description:
              "Si el robot intenta pagar con BNPL, Klarna u otro método que tú no aceptas para robots, lo paramos.",
          },
          "R023.refund-abuse-guard": {
            category: "postsale",
            title: "Vigilar devoluciones excesivas",
            description:
              "Si el asistente tiene un ratio de devoluciones muy alto en los últimos 90 días, le restringimos el acceso.",
          },
          "R024.dispute-history-guard": {
            category: "postsale",
            title: "Bloquear si tiene disputas recientes",
            description:
              "Si el asistente ha abierto muchas reclamaciones últimamente, no le dejamos comprar hasta que se resuelvan.",
          },
          "R025.sensitive-delivery-address": {
            category: "postsale",
            title: "Dirección de envío sospechosa",
            description:
              "Si el paquete va a un apartado de correos o a un almacén de reenvío, añadimos una revisión.",
          },
          "R026.subscription-autorenew-guard": {
            category: "postsale",
            title: "Suscripción sin consentimiento de renovación",
            description:
              "Si el robot quiere activar una suscripción y el cliente no ha dado el OK explícito a la renovación, lo paramos.",
          },
          "R027.gift-card-stored-value": {
            category: "postsale",
            title: "Compra de tarjetas regalo",
            description:
              "Puedes limitar o bloquear que los robots compren tarjetas regalo o productos de valor almacenado.",
          },
          "R028.b2b-po-guard": {
            category: "postsale",
            title: "Pedido B2B sin orden de compra",
            description:
              "Si el pedido es empresarial y no incluye un número de orden de compra válido, lo paramos.",
          },
          "R035.max-order-value": {
            category: "transaction",
            title: "Limitar el importe total del pedido",
            description:
              "Bloquea los pedidos de agentes cuyo total supere el límite que fijes. Un tope financiero del comercio.",
          },
          "R036.max-line-item-value": {
            category: "transaction",
            title: "Limitar el importe por línea",
            description:
              "Bloquea cualquier línea del carrito que supere el importe que fijes.",
          },
          "R038.max-items-per-order": {
            category: "transaction",
            title: "Limitar el número de artículos por pedido",
            description:
              "Bloquea los pedidos de agentes con más artículos de los que permitas. Control anti-acaparamiento.",
          },
          "R039.max-quantity-per-sku": {
            category: "transaction",
            title: "Limitar la cantidad por producto",
            description:
              "Bloquea comprar más unidades del mismo SKU de las que permitas.",
          },
          "R034.sku-blocklist": {
            category: "transaction",
            title: "Bloquear productos concretos (SKU) para agentes",
            description:
              "Impide que los agentes compren los productos exactos que indiques por SKU.",
          },
          "R032.category-blocklist": {
            category: "transaction",
            title: "Bloquear categorías concretas para agentes",
            description:
              "Impide que los agentes compren productos de las categorías que elijas (p. ej. bienes restringidos o de riesgo alto).",
          },
          "R048.no-digital-goods-for-agents": {
            category: "transaction",
            title: "Bloquear bienes digitales para agentes",
            description:
              "Impide que los agentes compren tarjetas regalo y otros productos digitales o de valor almacenado.",
          },
          "R043.agent-checkout-approval-required": {
            category: "postsale",
            title: "Requerir tu aprobación para checkouts de agente",
            description:
              "Las compras de agentes quedan en espera de tu aprobación manual antes de completarse. Añade una validación humana.",
          },
          "R029.merchant-preset": {
            category: "general",
            title: "Perfil de seguridad de tu tienda",
            description:
              "Elige entre 'abierto', 'equilibrado', 'estricto' o 'regulado' para aplicar un conjunto de reglas predefinido.",
          },
          "R030.simple-controls": {
            category: "general",
            title: "Controles básicos: importe máximo y países",
            description:
              "Fija un importe máximo por pedido y una lista de países permitidos. Rápido de configurar.",
          },
        },
      },
      seguridad: {
        title: "Seguridad de tu tienda",
        protectionSection: "¿Está protegida tu tienda?",
        errorChecking: "No se pudo comprobar la seguridad:",
        notActivatedTitle: "Protección aún no activada",
        notActivatedDesc:
          "Tu tienda todavía no tiene sello de confianza. Actívalo desde Inicio para empezar a firmar tus ventas a agentes de compra.",
        protectionBadge: "Sí, todo en orden",
        protectionDesc:
          "Tu tienda tiene protección activa. Cada venta realizada por un agente de compra queda registrada y firmada. Cualquier intento de manipulación queda registrado y se puede detectar.",
        lastRenewal: "Última renovación: hace {{days}} días",
        graceKeysNotice:
          "Hay {{count}} protección anterior que sigue siendo válida para comprobar ventas pasadas. No necesitas hacer nada — desaparece sola en los próximos días.",
        activitySection: "¿Qué ha pasado en tu tienda?",
        activitySectionDesc:
          "Aquí ves un registro de todo lo que ha pasado en tu tienda: quién entró, qué vendiste, qué pedidos se bloquearon y cuándo se renovó tu sello.",
        chainOk:
          "✓ El registro es correcto — nadie ha tocado el historial de tu tienda.",
        chainBroken:
          "⚠ Algo no cuadra en el historial de tu tienda. Esto es raro. Por favor contacta con soporte cuanto antes.",
        errorLoadingActivity: "No se pudo cargar el historial:",
        activityEmpty: "Aún no hay actividad registrada.",
        activityEmptyDesc: "Cuando ocurra algo en tu tienda, aparecerá aquí.",
        tableLabel: "Historial de actividad",
        colWhen: "Cuándo",
        colFrom: "Desde dónde",
        colResult: "Resultado",
        colWhat: "Qué pasó",
        colBuyerType: "Tipo de comprador",
        sourceShopify: "Desde Shopify",
        sourceWp: "Desde WordPress",
        sourcePortal: "Desde el portal",
        sourceApi: "Sistema automático",
        sourceMcp: "Comprador automático",
        sourceWebhook: "Notificación automática",
        outcomeAllowed: "✓ Aprobado",
        outcomeBlocked: "✗ Bloqueado",
        outcomeSessionRenewed: "Sesión renovada",
        buyerBot: "🤖 Robot automático",
        buyerHuman: "👤 Persona",
        viewMoreActivity: "Ver más actividad →",
      },
      settings: {
        title: "Ajustes",
        failureModeSection: "Modo de fallo del sistema de enforcement",
        failureModeSectionDesc:
          "Controla el comportamiento cuando la API de Trusteed no está disponible. Las reglas de nivel 1 (R001, R007) siempre se aplican sin conexión.",
        configSaved: "Configuración guardada correctamente.",
        errorSavingConfig: "No se pudo guardar la configuración:",
        optionBalanced: "Equilibrado (recomendado)",
        optionBalancedHint:
          "Reglas críticas (R001, R007) siempre activas; reglas avanzadas permiten el pedido si hay problemas de red.",
        optionStrict: "Estricto",
        optionStrictHint:
          "Bloquea todos los pedidos si no se puede verificar al agente. Máxima protección.",
        optionPermissive: "Permisivo",
        optionPermissiveHint:
          "Solo registra incidencias. Los pedidos nunca se bloquean por problemas de red.",
        paymentSection: "¿Cómo quiero que me paguen los agentes de compra?",
        paymentSectionNotice:
          "Cuando un agente de compra hace un pedido, Trusteed intenta cobrar en el orden que tú eliges aquí. Si el primero falla, prueba con el siguiente. Para cambiar este orden contacta con soporte o accede al portal principal de Trusteed.",
        paymentErrorLoading: "No se pudo cargar la configuración:",
        paymentNoRails:
          "Aún no tienes ningún método de pago activo. Ve a Merchant Center → Pagos para conectar Stripe, PayPal u otro proveedor.",
        paymentOrder: "Orden de cobro:",
        paymentRailEnabled: "✅ Activo",
        paymentRailDisabled: "❌ Desactivado",
        paymentRailMin: "Mínimo: {{amount}}",
        paymentRailMax: "Máximo: {{amount}}",
        paymentRailCurrencies: "Solo: {{currencies}}",
        paymentChangeNote:
          "Para cambiar el orden o activar/desactivar formas de pago, contacta con tu gestor de Trusteed.",
        railStripe: "Pago con tarjeta (Stripe)",
        railX402: "Pago en criptomoneda (x402)",
        railAcp: "Pago ACP",
        railPaypal: "PayPal",
        railEidas: "eIDAS",
      },
      merchantCenter: {
        title: "Merchant Center",
        showingStore: "Mostrando datos de tienda #{{id}}",
        tabsAriaLabel: "Merchant Center tabs",
        tabOrders: "Pedidos",
        tabPayments: "Pagos",
        tabAgents: "Agentes",
        tabCheckout: "Checkout",
        tabCertNlweb: "Certificación & NLWeb",
        loading: "Cargando...",
      },
      trustAuditLog: {
        bookDescription:
          "📋 El libro de registro de tu tienda. Aquí queda apuntado todo lo que ocurre: cuándo entró un agente de compras a tu tienda, qué hizo y si salió bien o mal. Si algo falla, usa los filtros de abajo para encontrar exactamente qué pasó y cuándo.",
        integrityVerified: "Integridad verificada",
        integrityError: "Error de integridad en la cadena de auditoría",
        filterAgentId: "Agent ID",
        filterAgentIdAria: "Filtrar por agentId",
        filterSource: "Todas las fuentes",
        filterSourceAria: "Filtrar por fuente",
        filterSourcePortal: "Portal",
        filterSourceShopify: "Shopify Embed",
        filterSourceWp: "WP Embed",
        filterSourceApi: "API",
        filterSourceMcp: "MCP",
        filterSourceWebhook: "Webhook",
        filterDateFrom: "Fecha desde",
        filterDateTo: "Fecha hasta",
        errorLoading: "No se pudo cargar el audit log:",
        tableLabel: "Audit log de Trust",
        colDate: "Fecha",
        colSource: "Fuente",
        colBucket: "Bucket",
        colTool: "Tool",
        colOutcome: "Outcome",
        colAgentId: "Agent ID",
        noEntries: "Sin entradas de audit para los filtros seleccionados.",
        loadMore: "Cargar mas",
        sourceLabels: {
          portal: "Portal",
          "shopify-embed": "Shopify Embed",
          "wp-embed": "WP Embed",
          api: "API",
          mcp: "MCP",
          webhook: "Webhook",
        },
        outcomeLabels: {
          ok: "OK",
          success: "Correcto",
          allow: "Permitido",
          deny: "Bloqueado",
          blocked_by_overlay: "Bloqueado",
          token_refresh_seamless: "Sesión renovada",
          error: "Error",
        },
      },
      trustKeys: {
        description:
          '🔑 Las llaves de tu tienda. Estas claves son las "firmas" que el sistema le pone a cada comprobante de venta para que no se pueda manipular sin que se detecte. Cámbialas cada 90 días para mantener todo seguro, igual que cambias la cerradura de vez en cuando.',
        ariaLoading: "Cargando claves",
        errorLoading: "No se pudo cargar el estado de claves:",
        notActivatedTitle: "Aún sin sello de confianza",
        notActivatedDesc:
          "Tu tienda todavía no ha generado su clave de firma. Activa tu protección desde Inicio; cuando esté activa, los detalles de la clave aparecerán aquí.",
        overdueWarning:
          "Recomendamos renovar la clave — llevan {{days}} días sin renovarse.",
        activeKeyTitle: "Sello de confianza activo",
        fieldIdentifier: "Identificador",
        fieldLastRenewal: "Última renovación",
        fieldDaysActive: "Días activo",
        fieldDaysActiveSuffix: "días",
        retiredKeysTitle: "Sellos anteriores (aún válidos)",
        retiredKeysAriaLabel: "Sellos anteriores aún válidos",
        retiredColIdentifier: "Identificador",
      },
      trustReceiptDetail: {
        ariaLabel: "Detalle de Trust Receipt",
        title: "Detalle de Receipt",
        closeAriaLabel: "Cerrar panel",
        fieldReceiptId: "ID de recibo",
        fieldBucket: "Categoría",
        fieldTool: "Herramienta",
        fieldAgentId: "ID de agente",
        fieldSigningKey: "Clave de firma (kid)",
        fieldInputHash: "Hash de entrada",
        fieldOutputHash: "Hash de salida",
        fieldDate: "Fecha",
        showJws: "Ver JWS completo",
        hideJws: "Ocultar JWS completo",
        copyJwksUrl: "Copiar URL JWKS",
        copied: "¡Copiado!",
        closeBtn: "Cerrar",
        downloadBundle: "Descargar comprobante (ZIP)",
        downloading: "Preparando…",
        downloadHint:
          "Prueba de integridad verificable — no es, por sí sola, evidencia de disputa para tu banco o proveedor de pagos.",
        downloadErrorV10: "Solo disponible para comprobantes v1.1.",
        downloadErrorLegacy:
          "Este comprobante no es exportable de forma offline.",
        downloadErrorGeneric: "No se pudo descargar el comprobante.",
      },
      trustReceiptsList: {
        description:
          "🤖 Compras hechas por agentes de compras. Cuando un agente de compras automático entra a tu tienda, aquí queda el comprobante firmado — como un ticket de caja, pero para ventas automáticas. Haz clic en una fila para ver todos los detalles de esa venta.",
        pacoBenefits:
          "✅ ¿Para qué te sirve? Si un agente de compra dice que no hizo el pedido, este comprobante — a prueba de manipulación y verificable de forma independiente — es un respaldo sólido: puedes descargarlo y aportarlo a tu banco o abogado. Por sí solo no sustituye a la evidencia que te pidan en una disputa. Nosotros lo guardamos entre 5 y 10 años según la ley de tu país — sin que tengas que hacer nada.",
        pacoRegulation:
          "🏛️ Nivel técnico: tus comprobantes están diseñados siguiendo los estándares de firma electrónica de Europa (eIDAS), Estados Unidos (ESIGN) y Reino Unido (UK DIATF), con firma digital + sello de tiempo. Aún no son firma ni sello cualificados (eso requiere una autoridad certificadora en la lista de confianza de la UE): sirven como prueba de integridad verificable que puedes aportar como respaldo, no como evidencia de disputa lista para banco o tribunal. Lo guardamos entre 5 y 10 años según la ley de tu país, sin que hagas nada.",
        learnMore: "Conoce como funciona y para que sirve",
        filterBuyerId: "ID del comprador",
        filterBuyerIdAria: "Filtrar por ID del comprador",
        filterTypeTodos: "Todos los tipos",
        filterTypeDiscovery: "Búsqueda",
        filterTypeCustomer: "Consulta",
        filterTypeCheckout: "Compra",
        filterTypeAria: "Filtrar por tipo",
        filterDateFrom: "Fecha desde",
        filterDateTo: "Fecha hasta",
        errorLoading: "No se pudieron cargar las ventas:",
        tableLabel: "Lista de ventas a agentes de compra",
        colNumber: "Nº",
        colOperation: "Operación",
        colType: "Tipo",
        colBuyer: "Comprador",
        colDate: "Fecha",
        noEntries: "Sin ventas para los filtros seleccionados.",
        loadMore: "Cargar mas",
        bucketDiscovery: "Búsqueda",
        bucketCustomer: "Consulta",
        bucketCheckout: "Compra",
      },
      merchantAgentsPanel: {
        title: "Agentes",
        description:
          "Aquí ves los agentes de compra que ya han intentado comprar en tu tienda. Puedes vetarlos, ponerlos en vigilancia o dejarles comprar libremente.",
        errorLoading: "No se pudieron cargar los agentes:",
        countersCanBuy: "Pueden comprar",
        countersWatching: "Los vigilo",
        countersBanned: "Vetados",
        tableLabel: "Lista de agentes de compra",
        colWho: "Quién es",
        colStatus: "Estado",
        colReputation: "Reputación",
        colActivity: "Actividad",
        colActions: "Qué quiero hacer",
        emptyTitle: "Aún no tienes agentes de compra conectados",
        emptyDesc:
          "Cuando un agente quiera comprar en tu tienda, aparecerá aquí.",
        defaultAgentName: "Comprador automático",
        callsLast30d: "{{count}} compras",
        callsLast30dSuffix: "últimos 30 días",
        stateCanBuy: "Puede comprar",
        stateWatching: "Lo estoy vigilando",
        stateBanned: "Vetado",
        statePending: "Pendiente de revisar",
        noData: "Sin datos",
        trustVeryReliable: "Muy de fiar",
        trustAcceptable: "Aceptable — vigílalo",
        trustLow: "Poca confianza — ojo",
        revokeBtn: "🚫 Vetar — no puede comprar",
        probationBtn: "👁️ Vigilar — lo reviso yo",
        allowBtn: "✅ Dejar comprar",
        closeNotification: "Cerrar notificación",
        toastRevoked: "Agente vetado",
        toastProbation: "Agente en vigilancia",
        toastAllowed: "Agente puede comprar",
        toastError: "Error al actualizar agente",
        auditReason: "Cambiado desde el panel del comercio ({{action}})",
        guideTitle: "¿Cómo sé si un agente de compra es de fiar?",
        guide80: "80 - 100 puntos:",
        guide80Desc: "Muy de fiar. Lleva tiempo comprando sin problemas.",
        guide50: "50 - 79 puntos:",
        guide50Desc:
          "Aceptable pero vigílalo. Puede tener algún incidente menor.",
        guide0: "0 - 49 puntos:",
        guide0Desc:
          "Poca confianza. Trusteed te recomienda vetarlo o ponerlo en vigilancia.",
        notifications: "Notificaciones",
      },
      merchantCertNlweb: {
        errorCert: "Error al cargar certificación.",
        errorNlweb: "Error al cargar estado NLWeb.",
        errorWebmcp: "Error al cargar estado WebMCP.",
        certTitle: "Certificación",
        nlwebTitle: "NLWeb",
        webmcpTitle: "WebMCP",
        nlwebStatus: "Estado",
        nlwebLastIndex: "Última indexación",
        nlwebProductsIndexed: "Productos indexados",
        nlwebReindex: "Re-indexar",
        nlwebReindexing: "Iniciando...",
        nlwebReindexSuccess: "Re-indexación iniciada",
        nlwebReindexError: "Error al iniciar re-indexación",
        webmcpStatus: "Estado",
        webmcpEnabled: "Habilitado",
        webmcpDisabled: "Deshabilitado",
        webmcpAdapter: "Adaptador detectado",
        webmcpLastConnection: "Última conexión",
        notifications: "Notificaciones",
        closeNotification: "Cerrar notificacion",
        certStatusLabels: {
          active: "Activa",
          expiring_soon: "Caduca pronto",
          expired: "Caducada",
          pending: "Pendiente",
          not_applicable: "No aplica",
        },
        nlwebStatusLabels: {
          idle: "Inactivo",
          indexing: "Indexando",
          error: "Error",
          never_indexed: "Nunca indexado",
        },
      },
      merchantCheckoutConfig: {
        description:
          "Configuración de los métodos de pago habilitados para agentes en checkout.",
        ariaLoading: "Cargando configuración",
        errorLoading: "No se pudo cargar la configuración:",
        railsAriaLabel: "Rails de checkout",
        enabled: "Habilitado",
        disabled: "Deshabilitado",
        noRails:
          "Aún no hay rails de pago configurados. Conecta un método de pago para habilitar el checkout para agentes.",
      },
      merchantOrders: {
        errorLoading: "No se pudieron cargar los pedidos:",
        tableLabel: "Lista de pedidos",
        colPlatform: "Plataforma",
        colOrderId: "Order ID",
        colStatus: "Estado",
        colTotal: "Total",
        colDate: "Fecha",
        noOrders: "No hay pedidos disponibles.",
        loadMore: "Cargar más",
      },
      merchantPaymentMethods: {
        description:
          "Conecta los métodos de cobro que aceptas de agentes de compra. Puedes activarlos, desactivarlos o desconectarlos en cualquier momento.",
        methodDescriptions: {
          stripe:
            "Procesador de tarjetas global. Acepta Visa, Mastercard, Amex y métodos locales. Conéctalo con tu Secret Key.",
          x402: "Pagos en USDC sobre Base/Solana vía protocolo x402. Liquidación on-chain en segundos.",
          acp: "Pagos delegados por agentes IA vía ACP (powered by Stripe). Para checkout iniciado por agente.",
          paypal: "Acepta pagos con PayPal. Próximamente disponible.",
          visa_vic:
            "Acepta pagos con Visa mediante credenciales digitales verificadas. Próximamente disponible.",
          mastercard_mcap:
            "Acepta pagos con Mastercard mediante agentes IA. Próximamente disponible.",
          kyapay:
            "Compra ahora, paga después. Soporte para Europa (eurozona). Próximamente disponible.",
        },
        errorLoading: "No se pudieron cargar los métodos de pago:",
        sectionAvailable: "Disponibles ahora",
        sectionComingSoon: "Próximamente",
        noMethods: "No hay métodos de pago disponibles en este momento.",
        comingSoonLabel: "Disponible pronto",
        statusConnected: "Conectado",
        statusVerifying: "Verificando",
        statusError: "Error",
        statusDisabled: "Desactivado",
        statusAvailable: "Sin conectar",
        connectApiKey: "Clave secreta de API",
        connectWallet: "Dirección de cartera",
        connectBtn: "Conectar",
        connecting: "Conectando…",
        enableBtn: "✅ Activar",
        disableBtn: "Desactivar",
        disconnectBtn: "🔌 Desconectar",
        actionConnected: "Conectado",
        actionReconnected: "Reconectado",
        actionDisconnected: "Desconectado",
        actionEnabled: "Activado",
        actionDisabled: "Desactivado",
        actionError: "Error",
        verifiedAt: "Verificado: {{date}}",
        closeBtn: "Cerrar",
        notifications: "Notificaciones",
      },
      shopSwitcher: {
        superAdmin: "Super-admin",
        superAdminTitle: "Super-administrador — acceso a todas las tiendas",
        changeSrOnly: "Cambiar tienda",
        shopLabel: "Tienda #{{id}}",
      },
      operatorsInstallations: {
        title: "Operator agents",
        description:
          "Operator agents act on this store on your behalf. Every privileged action is signed by your store key and logged in Trust Center.",
        errorLoading: "Failed to load installations: ",
        errorUnknown: "Unknown error",
        loadingAria: "Loading installations",
        tableLabel: "Operator installations",
        colVendorApp: "Vendor / App",
        colVersion: "Version",
        colModelHash: "Model hash",
        colPlatform: "Platform",
        colStatus: "Status",
        colInstalled: "Installed",
        colLastAction: "Last action",
        colReputation: "Reputation (V/A/I)",
        noInstallations:
          "No operator agents installed. Visit Trust Center to add.",
        justNow: "just now",
      },
      support: {
        buttonTitle: "Reportar un problema",
        modalTitle: "¿Algo no funciona?",
        description:
          "Cuéntanos qué pasó con tus propias palabras. Incluiremos automáticamente información técnica para que podamos ayudarte más rápido.",
        placeholder:
          "Ej: Al entrar en 'Mis ventas' la página se queda en blanco…",
        autoLogsNote:
          "Se adjuntarán automáticamente los últimos errores del panel (sin datos de clientes).",
        send: "Enviar",
        sending: "Enviando…",
        successTitle: "¡Ticket enviado!",
        ticketLabel: "Número de ticket:",
        successHint:
          "Recibirás respuesta en el correo asociado a tu cuenta. Normalmente respondemos en menos de 24 h.",
      },
    },
    Hh = {
      common: {
        loading: "Loading...",
        error: "Error",
        errorUnknown: "Unknown error",
        save: "Save",
        cancel: "Cancel",
        close: "Close",
        back: "Back",
        retry: "Try again",
        active: "Active",
        inactive: "Inactive",
        enabled: "Enabled",
        disabled: "Disabled",
        noData: "No data",
        notifications: "Notifications",
        closeNotification: "Close notification",
        loadingMore: "Loading…",
        loadMore: "Load more",
        configSaved: "Settings saved successfully.",
      },
      inicio: {
        title: "How is my store doing today?",
        errorLoadingSummary: "Could not load summary:",
        reputationSection: "Your reputation as a seller",
        scoreLabelComputing: "Computing…",
        scoreLabelExcellent: "Excellent",
        scoreLabelAcceptable: "Acceptable",
        scoreLabelNeedsAttention: "Needs attention",
        scoreDescComputing:
          "Trusteed is analysing your store. Come back in a few minutes.",
        scoreDescExcellent:
          "Your store has an impeccable reputation among shopping agents.",
        scoreDescAcceptable:
          "Your store is doing well, but there is room for improvement. Enable more security rules.",
        scoreDescNeedsAttention:
          "Some shopping agents have doubts about your store. Review your rules.",
        scoreAriaLoading: "Loading score",
        updatedAt: "Updated on {{date}} UTC",
        scoreLow: "0 — Low",
        scoreMid: "50 — Medium",
        scoreHigh: "100 — Excellent",
        manageReputation: "Manage Reputation →",
        viewActivity: "View activity history →",
        securitySection: "Is your store protected?",
        securityProtected: "Protected",
        securityProtectedDesc:
          "Your store has active protection. Every sale to a shopping agent is recorded and signed.",
        securityActiveKey: "Active key: {{kid}}",
        securityGraceKeys: "{{count}} in grace period",
        securityNotActive: "Not activated",
        securityNotActiveDesc:
          "Your store does not yet have the trust seal activated. Shopping agents cannot see that you are trustworthy.",
        securityActivate: "Activate my protection →",
        salesSection: "Latest sales to shopping agents",
        salesEmpty: "You have no sales to shopping agents yet.",
        salesEmptyDesc:
          "When a robot or virtual assistant buys from your store, it will appear here.",
        salesTableLabel: "Latest AI sales",
        salesColNumber: "No.",
        salesColType: "Type",
        salesColBuyer: "Buyer",
        salesColDate: "Date",
        viewAllOrders: "View all my orders →",
        bucketCheckout: "Purchase",
        bucketCustomer: "Query",
        bucketDiscovery: "Search",
      },
      trustCenter: {
        title: "Trust Center",
        tabOverview: "Resumen",
        tabReceipts: "Ventas IA",
        tabKeys: "Claves",
        tabAudit: "Auditoría",
        tabsAriaLabel: "Trust Center tabs",
        loading: "Loading...",
      },
      trustReceipts: {
        panelTitle: "Trust panel",
        scoringMethodLink: "How we determine your store score",
        errorLoadingData:
          "Your store data will load as soon as the service is available.",
        noData: "No data.",
        scoreLow: "0 — Low",
        scoreMid: "50 — Medium",
        scoreCap: "Cap: {{cap}}/100",
        scoreHigh: "90–100 — Excellent",
        scoreCurrent: "Current cap: {{cap}}/100",
        scoreComputing: "Computing…",
        scoreExcellent: "Excellent",
        scoreImprovable: "Improvable",
        scoreNeedsAttention: "Needs attention",
        coldStartDesc:
          "Your store is just getting started. This score rises as you complete the basic steps and receive your first sales.",
        humanEstablishedDesc:
          "You have a good track record as a seller. Your score can reach up to 85/100 before your first sale to a shopping agent.",
        scoreHighDesc:
          "Your store has an impeccable reputation. Agents fully trust you.",
        scoreMidDesc:
          "Your store is well rated among agents. Review the yellow areas to keep improving.",
        scoreLowDesc:
          "Some buying agents have doubts. Act on the red areas as soon as possible.",
        computedAt: "Computed on {{date}} UTC",
        pendingActionsOne: "pending improvement",
        pendingActionsMany: "pending improvements",
        breakdownTitle: "Score breakdown",
        coldStartNotice:
          "Your store is just getting started. The seller history score and agent experience will be available when you have more activity — this is normal, not negative.",
        notApplicable: "Not applicable yet",
        weightLabel: "weight {{pct}}%",
        noDataLabel: "No data",
        countWillActivate: "Will count when activated:",
        measuredNow: "What is measured at your current stage",
        checklistTitle: "What can I do to improve my score?",
        checklistProgress: "{{done}} of {{total}} completed",
        checklistMissingMany:
          "— you still need {{count}} things to reach the maximum",
        checklistMissingOne:
          "— you still need {{count}} thing to reach the maximum",
        checklistAllDone: "— everything is in order to get started",
        checklistNow: "Right now",
        checklistAutoTitle: "With your first sales",
        checklistAutoNote:
          "These factors are measured automatically when orders start coming in. You don't need to do anything — the system collects them automatically.",
        checklistAutoActiveNote:
          "These factors are now being measured from your real sales and already count toward your score.",
        practicesTitle: "Three things that always work",
        faqTitle: "Questions everyone asks",
        securityTitle: "Is your store protected?",
        securityActiveDesc:
          "Every sale to a shopping agent is recorded and sealed.",
        securityProtectionActive: "Active protection.",
        securityRotationWarning:
          "It has been more than {{days}} days since protection was last renewed. Contact support if you notice anything unusual.",
        securityNotActive:
          "Your store does not yet have the trust seal activated.",
        securityActivate: "Activate my protection →",
        goToKeys: "Go to Keys",
        milestoneText: {
          no_store: "Open your store to start building your reputation.",
          cold_start:
            "Complete onboarding and make your first sales to raise your score.",
          human_established:
            "Make your first sales to shopping agents to unlock the top score tier.",
        },
        recText: {
          activate_security_key: "Activate your trust seal (signing key).",
          add_return_policy: "Add a clear return policy.",
          add_shipping_policy: "Add an estimated shipping time.",
          complete_first_agent_sale:
            "Complete your first sale to a shopping agent.",
          more_agent_sales: "Get more sales to shopping agents.",
          improve_fulfillment_rate: "Improve your on-time shipping rate.",
          reduce_disputes: "Reduce disputes and refunds.",
          external_assurance_unavailable:
            "External assurance is not available yet.",
        },
        checklistSello: "Security seal active",
        checklistSelloDetail:
          "Ensures every order from a shopping agent is signed. Without it, agents do not trust your store.",
        checklistCompliance: "No errors in your payment methods",
        checklistComplianceDetail:
          "You have an error in your payment method. Shopping agents cannot complete their purchases.",
        checklistComplianceAction: "Open payment settings in WooCommerce",
        checklistDevoluciones: "Returns policy",
        checklistDevolucionesDetail:
          "How many days does the customer have? Do you cover return shipping? If it is not written down, agents count it against you.",
        checklistDevolucionesAction: "Open policies page in WooCommerce",
        checklistEnvio: "Estimated shipping time",
        checklistEnvioDetail:
          "Specify how many days you take to ship. If you don't, it looks like you're unsure about your own process.",
        checklistEnvioAction: "Open shipping settings in WooCommerce",
        checklistCatalogo: "Catalogue with photos, prices and descriptions",
        checklistCatalogoDetail:
          "Your products need a photo, real price, updated stock and a description. This counts from day one.",
        checklistCatalogoAction: "Open product list",
        checklistFulfillment: "On-time shipping",
        checklistFulfillmentDetail:
          "Measured automatically — the % that arrive within the promised timeframe.",
        checklistDisputes: "Few disputes and refunds",
        checklistDisputesDetail:
          "Measured automatically — the % of orders with a claim in the last 90 days.",
        storeAgeLine: "Your store has been open for",
        storeActive: "active",
        storeYear: "year",
        storeYears: "years",
        storeDays: "days",
        practice1Title: "Keep your catalogue up to date",
        practice1Body:
          "Update stock, prices and photos so automated buyers see what you actually have. If what you show doesn't match what you have, trust goes down.",
        practice2Title: "Put your return rules in writing",
        practice2Body:
          "How many days does the customer have to return? Do you cover return shipping? Store credit or full refund? If it's not written down, automated buyers count it against you.",
        practice3Title: "Ship fast and without surprises",
        practice3Body:
          "When an order arrives late or a claim is made, your score drops. You don't need to be Amazon — just do what you promise. If you say 3 days, ship in 3 days.",
        faq1Q: "Is this score the same as the star ratings customers give me?",
        faq1A:
          "No. Stars are given by people who buy. This score is calculated by Trusteed by looking at how your store works internally: whether stock matches, whether orders arrive, whether there are claims. It's a completely different thing.",
        faq2Q: "If I have 100 points, will I sell more?",
        faq2A:
          "Not directly. Having a high score means more automated buyers will find your store and trust it. But whether they buy or not depends on your prices, your catalogue and what you sell.",
        faq3Q: "Can I lose visibility before my store gets closed?",
        faq3A:
          "Yes. If the score drops a lot, you first appear less in searches. If it keeps dropping, the store gets hidden. And if it's not fixed, it can be suspended. That's why it's best to act before it reaches that point.",
        humanOrders: "order",
        humanOrdersPlural: "orders",
        humanOrdersOf: "from human customers",
        agentSale: "sale",
        agentSalePlural: "sales",
        agentSalesOf: "to shopping agents",
        confidenceInsufficient: "Complete the setup to see your score",
        confidenceColdStart: "Insufficient history",
        confidenceHumanEstablished: "Seller with human history",
        confidenceAgentThin: "First agent sales",
        confidenceAgentVerified: "Verified with agents",
        unlockAreaDefault:
          "This area will activate when you have more activity in your store.",
        unlockMerchantColdStart:
          "Activates when you have at least 5 customer sales. You don't have enough history yet.",
        unlockAgentColdStart:
          "Activates with your first sales to shopping agents.",
        unlockAgentHuman: "Activates with your first sales to shopping agents.",
        dimMerchantReliability: "🏪 What is your track record as a seller?",
        dimAgenticReadiness: "⚙️ Is your store ready for shopping agents?",
        dimAgenticEvidence:
          "🤝 What experience have agents had with your store?",
        subDim: {
          setupQuality: "Catalog, policies & pricing",
          protocolSecurity: "Security seal (digital signature)",
          complianceHealth: "No errors in your payment methods",
          integrationFreshness: "Catalog up to date & synced",
          fulfillmentPerformance: "Orders shipped on time",
          disputeAndRefundHealth: "Few claims or returns",
          orderCompletionHealth: "Orders completed without cancellation",
          catalogOperationalQuality: "Complete & up-to-date catalog",
          storeAgeConfidence: "Store age & history",
          humanOrderVolumeConfidence: "Number of customers served",
          agentCheckoutSuccessRate: "Agent purchases completed",
          agentFulfillmentOnTimeRate: "On-time shipping to agent buyers",
          agentDisputeRefundHealth: "Few disputes on agent sales",
          receiptIntegrity: "Signed digital receipts",
          agentLatencyAndToolReliability: "Response speed to agents",
          repeatAgentSuccess: "Agents that come back to buy",
          fulfillmentRate: "Orders shipped on time",
          disputeRate: "Few complaints or returns",
          returnWindowClarity: "Return rules are written down",
          catalogCompleteness: "Products with photos, prices and stock",
          storeAgeBonus: "How long your store has been open",
          hasActiveKey: "Active digital signing key",
          hasShippingPolicy: "Shipping time is written down",
          hasReturnPolicy: "Return rules are written down",
          paymentMethodsSetup: "You can receive payments",
          mcpEndpointHealthy: "Shopping agents can reach your store",
          agentOrderSuccessRate: "Shopping agents complete their orders",
          trustReceiptCoverage: "Signed receipts for every shopping agent sale",
          agentConsentCompliance:
            "Shopping agents have accepted your store terms",
        },
      },
      paymentMethods: {
        title: "Payment methods",
        ariaLoading: "Loading payment methods",
        errorLoading: "Could not load payment methods:",
        noMethods: "No payment methods configured.",
        listAriaLabel: "Payment methods list",
        rotateBtnLabel: "Rotate credentials for {{label}}",
        rotateBtn: "Rotate credentials",
        dialogTitle: "Rotate credentials",
        dialogDesc:
          "Are you sure you want to rotate the credentials? This action cannot be undone.",
        confirmBtn: "Confirm",
        cancelBtn: "Cancel",
        rotating: "Rotating...",
        rotateSuccess: "Credentials rotated successfully",
        rotateErrorFallback: "Error rotating credentials",
        closeNotification: "Close notification",
        statePending: "Pending",
        stateActive: "Active",
        stateError: "Error",
        stateDisabled: "Disabled",
      },
      misVentas: {
        title: "My sales",
        tabOrders: "My orders",
        tabReceipts: "AI Sales",
        tabKeys: "Keys",
        tabAudit: "Audit",
        tabsAriaLabel: "My sales sections",
        ordersSection: "All my orders",
        ordersSectionDesc:
          "Here you can see all orders placed in your store, from both regular and shopping agents.",
        ordersErrorLoading: "Could not load orders:",
        ordersEmpty: "You have no orders registered yet.",
        ordersEmptyDesc:
          "When someone buys from your store, the order will appear here.",
        ordersTableLabel: "Orders list",
        colOrderNumber: "Order no.",
        colStore: "Store",
        colStatus: "Status",
        colTotal: "Total",
        colBuyer: "Who bought",
        colDate: "Date",
        platformShopify: "Shopify",
        platformWoo: "WooCommerce",
        platformAcp: "Other channel",
        statusCreated: "Created",
        statusUpdated: "Updated",
        statusPaid: "Paid",
        statusFulfilled: "Fulfilled",
        statusCancelled: "Cancelled",
        statusRefunded: "Refunded",
        buyerAuto: "🤖 Shopping agent #{{id}}",
        buyerHuman: "👤 Person",
        loadMoreOrders: "Load more orders →",
        receiptsSection: "Receipts from shopping agent sales",
        receiptsSectionDesc:
          "Every time a robot or virtual assistant buys from your store, a signed, timestamped receipt is generated.",
        receiptsBenefits:
          "✅ What is it for? If a buyer claims 'I never placed that order', this receipt — signed, timestamped and tamper-evident — is solid backup: you can download it and hand it to your bank or solicitor. On its own it does not replace the evidence they may require in a dispute. We store it automatically for as long as the law in your country requires (5–10 years), with nothing to do on your end.",
        receiptsRegulation:
          "🏛️ Technical level: these receipts are designed to follow the EU electronic-signature standard (eIDAS) and carry a digital signature plus a timestamp. They are not yet a qualified signature or timestamp (that requires a certificate authority on the EU trusted list): they count as a verifiable integrity proof, not ready-made dispute evidence for a bank or court.",
        receiptsErrorLoading: "Could not load receipts:",
        receiptsEmpty: "No automated sale receipts yet.",
        receiptsEmptyDesc:
          "When a shopping agent makes a purchase, the receipt will appear here automatically.",
        receiptsTableLabel: "AI Receipts",
        colReceiptNumber: "Receipt no.",
        colType: "Type",
        colAction: "Action",
        colBuyer2: "Buyer",
        bucketCheckout: "Purchase",
        bucketCustomer: "Query",
        bucketDiscovery: "Search",
      },
      misReglas: {
        title: "My Rules",
        infoNotice:
          "These rules are applied automatically to any buying agent that tries to purchase in your store. Enable or disable them with one click. The more you enable, the better protected your store will be.",
        errorNotice:
          "Rules are shown in provisional mode. Changes will be saved when the service is available.",
        rulesByCategoryTitle: "Rules by category",
        activeCountLabel: "{{active}} of {{total}} rules active",
        ruleActive: "Active",
        ruleInactive: "Inactive",
        blockedLast7d: "{{count}} blocked 7d",
        failOpenLast7d: "{{count}} fail-open 7d",
        toggleAriaActive: "Disable rule",
        toggleAriaInactive: "Enable rule",
        howItWorksTitle: "How do rules work?",
        howInactive:
          "Not applied. The buying agent passes without being evaluated by this criterion.",
        howInactiveLabel: "Inactive rule:",
        howActive:
          "Evaluated on every purchase. If the buying agent breaks it, they are blocked or placed under review depending on severity.",
        howActiveLabel: "Active rule:",
        howStats:
          "The badges next to each rule show how many buyers have been affected in the last 7 days.",
        howStatsLabel: "Real-time statistics:",
        ruleActivatedMsg: "Rule activated",
        ruleDeactivatedMsg: "Rule deactivated",
        ruleChangeError: "Could not change the rule. Please try again.",
        paramsTitle: "Parameters",
        paramsSaveBtn: "Save parameters",
        paramsResetBtn: "Reset",
        paramsCsvHint: "Comma-separated values",
        taxonomyUnavailable:
          "Shopify category search is unavailable on this platform",
        taxonomySearchPlaceholder: "Search category…",
        taxonomyRemoveLabel: "Remove {{name}}",
        r001r009Redundancy:
          "R009 will run in shadow mode (same verdict as R001 — first match wins). It will not change outcomes, only separate telemetry.",
        r002WithoutR001:
          "R001 is OFF, so R002 only blocks invalid tokens that are presented. Enable R001 to require a token on every request.",
        paramsRequiredHint:
          "Provide at least one of the listed parameters before enabling.",
        paramsRequiredError: "Required parameter missing:",
        params: {
          "R001.requireAgentId": "Require agent ID",
          "R001.merchantTier": "Merchant tier (high = always require agent ID)",
          "R004.maxKeyAgeHours": "Max key age (hours)",
          "R009.requireAgentId": "Require agent ID",
          "R010.minCompletedOrders": "Minimum completed orders",
          "R003.allowedCategories": "Allowed categories (mandate)",
          "R012.categories": "Blocked categories",
          "option.alcohol": "Alcohol",
          "option.tobacco": "Tobacco",
          "option.firearms": "Firearms",
          "option.pharmaceuticals": "Pharmaceuticals",
          "option.adult": "Adult content",
          "option.gambling": "Gambling",
          "option.cbd": "CBD",
          "option.electronics_high_value": "High-value electronics",
          "option.jewelry": "Jewelry",
          "option.crypto": "Crypto",
          "option.gift_cards": "Gift cards",
          "option.subscriptions": "Subscriptions",
          "R014.highRiskCountries": "High-risk countries (ISO-2)",
          "R014.maxCancellations": "Max cancellations",
          "R014.windowDays": "Window (days)",
          "R015.maxDeltaBps": "Max price delta (bps)",
          "R016.minStock": "Minimum stock",
          "R018.spikeMultiplier": "Spike multiplier",
          "R018.merchantAvgOrderCents": "Merchant avg order (cents)",
          "R018.maxItemCount": "Max items per cart",
          "R018.maxSingleSkuQty": "Max qty per SKU",
          "R021.minCompletedOrders":
            "Min completed orders before first purchase",
          "R022.allowedPaymentMethods": "Allowed payment methods",
          "R022.blockedPaymentMethods": "Blocked payment methods",
          "option.card": "Card",
          "option.paypal": "PayPal",
          "option.bnpl": "Buy now, pay later (BNPL)",
          "option.klarna": "Klarna",
          "option.afterpay": "Afterpay",
          "option.bank_transfer": "Bank transfer",
          "option.apple_pay": "Apple Pay",
          "option.google_pay": "Google Pay",
          "option.gift_card": "Gift card",
          "R023.windowDays": "Window (days)",
          "R023.maxRatio": "Max refund ratio (0–1)",
          "R024.windowDays": "Window (days)",
          "R024.maxDisputes": "Max disputes",
          "R025.blockPoBox": "Block PO Box addresses",
          "R025.blockFreightForwarder": "Block freight forwarders",
          "R026.requireConsent": "Require explicit consent",
          "R027.maxStoredValueCents": "Max stored value (cents)",
          "R028.requirePurchaseOrder": "Require purchase order",
          "R032.blockedCategoryIds": "Blocked categories",
          "R034.blockedSkus": "Blocked SKUs",
          "R035.maxCents": "Max order total (cents)",
          "R036.maxCentsPerLine": "Max amount per line (cents)",
          "R038.maxQuantity": "Max items per order",
          "R039.maxPerSku": "Max quantity per SKU",
          "R043.ttlMinutes": "Approval window (minutes)",
          "R048.blockedTypes": "Blocked digital types",
          "option.license_key": "License key",
          "option.downloadable": "Downloadable",
          "option.stored_value": "Stored value",
        },
        helpLinkLabel: "What does this rule do? →",
        helpBase: "https://trusteed.xyz/en/agent-rules",
        categoryLabels: {
          identity: "Identity",
          behavior: "Behavior",
          transaction: "Transaction",
          postsale: "Post-sale",
          general: "General",
          other: "Other",
        },
        rulesMeta: {
          "R001.verified-agent-required": {
            category: "identity",
            title: "Agent must identify itself",
            description:
              "If a robot or assistant wants to buy in your store, it must present its 'digital ID'. Without identification, no purchase.",
          },
          "R002.signature-spoof-block": {
            category: "identity",
            title: "Block forged signature",
            description:
              "If the robot tries to buy with a digital signature that doesn't match, we stop it immediately.",
          },
          "R003.mandate-boundary-match": {
            category: "identity",
            title: "Robot cannot spend more than its owner authorises",
            description:
              "If the assistant has a signed limit of €100 and adds €250 to the cart, the order is cancelled.",
          },
          "R004.new-key-friction": {
            category: "identity",
            title: "Very new identity key",
            description:
              "If the robot has a brand-new identity key, we wait a few hours before letting it buy.",
          },
          "R005.revoked-agent-block": {
            category: "identity",
            title: "Robot with revoked access",
            description:
              "If the assistant is on Trusteed's blacklist, it cannot buy in any store.",
          },
          "R006.provider-confidence-tier": {
            category: "identity",
            title: "Block buyers with poor reputation",
            description:
              "If the robot's identity provider tells us its confidence is very low, I don't let it buy.",
          },
          "R007.cross-merchant-abuse-signal": {
            category: "identity",
            title: "Robot that has already caused harm in other stores",
            description:
              "If the assistant has had serious problems in other Trusteed stores, we block it here too.",
          },
          "R008.scope-escalation-detection": {
            category: "identity",
            title: "Robot requests more permissions than authorised",
            description:
              "If the assistant tries to access areas of your store it doesn't have permission for, we stop it.",
          },
          "R009.agent-verification-required": {
            category: "behavior",
            title: "Agent verification required",
            description:
              "For certain operations, the assistant must prove its identity before continuing.",
          },
          "R010.new-agent-probation": {
            category: "behavior",
            title: "Robot with no history in your store",
            description:
              "If this is the first time this assistant buys here, we add a small extra check.",
          },
          "R011.repeat-failed-checkout": {
            category: "behavior",
            title: "Detect suspiciously fast purchases",
            description:
              "If the robot fails several times in a row at payment within a few minutes, we pause it.",
          },
          "R012.high-risk-category": {
            category: "behavior",
            title: "High-risk product category",
            description:
              "Some products (gift cards, wine, tobacco…) have a higher risk of fraud. We add caution.",
          },
          "R013.return-policy-guard": {
            category: "behavior",
            title: "Robot does not accept your return policy",
            description:
              "If the assistant has not accepted your store's return conditions, the order is paused.",
          },
          "R014.delivery-risk-guard": {
            category: "behavior",
            title: "High-risk shipping country",
            description:
              "If the package destination is on a list of problematic countries (sanctions, frequent fraud), we review it.",
          },
          "R015.price-change-guard": {
            category: "transaction",
            title: "Price changed significantly during the process",
            description:
              "If the price of an item rises or falls more than a configured % between adding to cart and paying, we stop it.",
          },
          "R016.stock-confidence-guard": {
            category: "transaction",
            title: "Insufficient stock for the robot",
            description:
              "If a product is about to run out of stock, we only let humans complete the purchase.",
          },
          "R017.coupon-discount-anomaly": {
            category: "transaction",
            title: "Detect repeated discount searching",
            description:
              "If the robot tests many discount coupons abnormally, we flag it as suspicious.",
          },
          "R018.cart-composition-guard": {
            category: "transaction",
            title: "Detect large or unusual orders",
            description:
              "If the cart total is much higher than your store's average order, we review it before approving.",
          },
          "R019.country-jurisdiction": {
            category: "transaction",
            title: "Country not permitted in your store",
            description:
              "If the robot tries to invoice from a country outside your list of authorised countries, the order is cancelled.",
          },
          "R020.business-hours": {
            category: "transaction",
            title: "Purchases only during business hours",
            description:
              "If you configure a business hours window, robots cannot complete orders outside those hours.",
          },
          "R021.first-purchase-with-merchant": {
            category: "postsale",
            title: "Robot's first purchase in your store",
            description:
              "If it has never bought here before, we add an extra review even if the robot is known in other stores.",
          },
          "R022.payment-rail-restriction": {
            category: "postsale",
            title: "Payment method not permitted",
            description:
              "If the robot tries to pay with BNPL, Klarna or another method you don't accept for robots, we stop it.",
          },
          "R023.refund-abuse-guard": {
            category: "postsale",
            title: "Monitor excessive returns",
            description:
              "If the assistant has a very high return rate in the last 90 days, we restrict its access.",
          },
          "R024.dispute-history-guard": {
            category: "postsale",
            title: "Block if recent disputes exist",
            description:
              "If the assistant has opened many claims recently, we don't let it buy until they are resolved.",
          },
          "R025.sensitive-delivery-address": {
            category: "postsale",
            title: "Suspicious delivery address",
            description:
              "If the package goes to a PO box or a re-shipping warehouse, we add a review.",
          },
          "R026.subscription-autorenew-guard": {
            category: "postsale",
            title: "Subscription without renewal consent",
            description:
              "If the robot wants to activate a subscription and the customer has not given explicit OK to renewal, we stop it.",
          },
          "R027.gift-card-stored-value": {
            category: "postsale",
            title: "Gift card or stored-value purchase",
            description:
              "You can limit or block robots from buying gift cards or stored-value products.",
          },
          "R028.b2b-po-guard": {
            category: "postsale",
            title: "B2B order without purchase order",
            description:
              "If the order is corporate and does not include a valid purchase order number, we stop it.",
          },
          "R035.max-order-value": {
            category: "transaction",
            title: "Cap the total order amount",
            description:
              "Blocks agent orders whose total exceeds the limit you set. A merchant-side financial guardrail.",
          },
          "R036.max-line-item-value": {
            category: "transaction",
            title: "Cap the amount per line item",
            description:
              "Blocks any single cart line above the amount you set.",
          },
          "R038.max-items-per-order": {
            category: "transaction",
            title: "Cap the number of items per order",
            description:
              "Blocks agent orders with more total items than you allow. Anti-hoarding control.",
          },
          "R039.max-quantity-per-sku": {
            category: "transaction",
            title: "Cap the quantity per product",
            description:
              "Blocks buying more units of the same SKU than you allow.",
          },
          "R034.sku-blocklist": {
            category: "transaction",
            title: "Block specific products (SKUs) for agents",
            description:
              "Stops agents from buying the exact products you list by SKU.",
          },
          "R032.category-blocklist": {
            category: "transaction",
            title: "Block specific categories for agents",
            description:
              "Stops agents from buying products in categories you choose (e.g. restricted or high-risk goods).",
          },
          "R048.no-digital-goods-for-agents": {
            category: "transaction",
            title: "Block digital goods for agents",
            description:
              "Stops agents from buying gift cards and other digital or stored-value products.",
          },
          "R043.agent-checkout-approval-required": {
            category: "postsale",
            title: "Require your approval for agent checkouts",
            description:
              "Agent purchases are held pending your manual approval before completing. Adds a human-in-the-loop gate.",
          },
          "R029.merchant-preset": {
            category: "general",
            title: "Store security profile",
            description:
              "Choose between 'open', 'balanced', 'strict' or 'regulated' to apply a predefined set of rules.",
          },
          "R030.simple-controls": {
            category: "general",
            title: "Basic controls: maximum amount and countries",
            description:
              "Set a maximum order amount and a list of allowed countries. Quick to configure.",
          },
        },
      },
      seguridad: {
        title: "Store security",
        protectionSection: "Is your store protected?",
        errorChecking: "Could not verify security:",
        notActivatedTitle: "Protection not activated yet",
        notActivatedDesc:
          "Your store does not have a trust seal yet. Activate it from Home to start signing your sales to shopping agents.",
        protectionBadge: "Yes, all in order",
        protectionDesc:
          "Your store has active protection. Every sale made by a shopping agent is recorded and signed. Any attempt to tamper with an order is recorded and detectable.",
        lastRenewal: "Last renewal: {{days}} days ago",
        graceKeysNotice:
          "There are {{count}} previous protection keys still valid for verifying past sales. You don't need to do anything — they expire automatically in the coming days.",
        activitySection: "What has happened in your store?",
        activitySectionDesc:
          "Here you can see a log of everything that has happened in your store: who entered, what you sold, which orders were blocked and when your seal was renewed.",
        chainOk:
          "✓ The log is correct — no one has tampered with your store history.",
        chainBroken:
          "⚠ Something is off in your store history. This is unusual. Please contact support as soon as possible.",
        errorLoadingActivity: "Could not load activity history:",
        activityEmpty: "No activity recorded yet.",
        activityEmptyDesc:
          "When something happens in your store, it will appear here.",
        tableLabel: "Activity history",
        colWhen: "When",
        colFrom: "From",
        colResult: "Result",
        colWhat: "What happened",
        colBuyerType: "Buyer type",
        sourceShopify: "From Shopify",
        sourceWp: "From WordPress",
        sourcePortal: "From the portal",
        sourceApi: "Automated system",
        sourceMcp: "Shopping agent",
        sourceWebhook: "Automated notification",
        outcomeAllowed: "✓ Approved",
        outcomeBlocked: "✗ Blocked",
        outcomeSessionRenewed: "Session renewed",
        buyerBot: "🤖 Automated robot",
        buyerHuman: "👤 Person",
        viewMoreActivity: "View more activity →",
      },
      settings: {
        title: "Settings",
        failureModeSection: "Enforcement system failure mode",
        failureModeSectionDesc:
          "Controls behaviour when the Trusteed API is unavailable. Level-1 rules (R001, R007) are always applied offline.",
        configSaved: "Settings saved successfully.",
        errorSavingConfig: "Could not save settings:",
        optionBalanced: "Balanced (recommended)",
        optionBalancedHint:
          "Critical rules (R001, R007) always active; advanced rules allow the order if there are network issues.",
        optionStrict: "Strict",
        optionStrictHint:
          "Blocks all orders if the agent cannot be verified. Maximum protection.",
        optionPermissive: "Permissive",
        optionPermissiveHint:
          "Only logs incidents. Orders are never blocked due to network issues.",
        paymentSection: "How do I want shopping agents to pay me?",
        paymentSectionNotice:
          "When a shopping agent places an order, Trusteed tries to charge in the order you choose here. If the first fails, it tries the next. To change this order contact support or access the main Trusteed portal.",
        paymentErrorLoading: "Could not load configuration:",
        paymentNoRails:
          "You have no active payment method yet. Go to Merchant Center → Payments to connect Stripe, PayPal or another provider.",
        paymentOrder: "Charge order:",
        paymentRailEnabled: "✅ Active",
        paymentRailDisabled: "❌ Disabled",
        paymentRailMin: "Minimum: {{amount}}",
        paymentRailMax: "Maximum: {{amount}}",
        paymentRailCurrencies: "Only: {{currencies}}",
        paymentChangeNote:
          "To change the order or enable/disable payment methods, contact your Trusteed manager.",
        railStripe: "Card payment (Stripe)",
        railX402: "Crypto payment (x402)",
        railAcp: "ACP payment",
        railPaypal: "PayPal",
        railEidas: "eIDAS",
      },
      merchantCenter: {
        title: "Merchant Center",
        showingStore: "Showing data for store #{{id}}",
        tabsAriaLabel: "Merchant Center tabs",
        tabOrders: "Orders",
        tabPayments: "Payments",
        tabAgents: "Agents",
        tabCheckout: "Checkout",
        tabCertNlweb: "Certification & NLWeb",
        loading: "Loading...",
      },
      trustAuditLog: {
        bookDescription:
          "📋 Your store's record book. Everything that happens is logged here: when a buying agent entered your store, what they did, and whether it went well or not. If something fails, use the filters below to find exactly what happened and when.",
        integrityVerified: "Integrity verified",
        integrityError: "Chain integrity error in the audit log",
        filterAgentId: "Agent ID",
        filterAgentIdAria: "Filter by agentId",
        filterSource: "All sources",
        filterSourceAria: "Filter by source",
        filterSourcePortal: "Portal",
        filterSourceShopify: "Shopify Embed",
        filterSourceWp: "WP Embed",
        filterSourceApi: "API",
        filterSourceMcp: "MCP",
        filterSourceWebhook: "Webhook",
        filterDateFrom: "Date from",
        filterDateTo: "Date to",
        errorLoading: "Could not load the audit log:",
        tableLabel: "Trust audit log",
        colDate: "Date",
        colSource: "Source",
        colBucket: "Bucket",
        colTool: "Tool",
        colOutcome: "Outcome",
        colAgentId: "Agent ID",
        noEntries: "No audit entries for the selected filters.",
        loadMore: "Load more",
        sourceLabels: {
          portal: "Portal",
          "shopify-embed": "Shopify Embed",
          "wp-embed": "WP Embed",
          api: "API",
          mcp: "MCP",
          webhook: "Webhook",
        },
        outcomeLabels: {
          ok: "OK",
          success: "Success",
          allow: "Allowed",
          deny: "Blocked",
          blocked_by_overlay: "Blocked",
          token_refresh_seamless: "Session renewed",
          error: "Error",
        },
      },
      trustKeys: {
        description: `🔑 Your store's keys. These keys are the "signatures" the system puts on each sale receipt so any tampering is detectable. Change them every 90 days to keep everything secure, just like you change a lock every now and then.`,
        ariaLoading: "Loading keys",
        errorLoading: "Could not load key status:",
        notActivatedTitle: "No trust seal yet",
        notActivatedDesc:
          "Your store has not generated its signing key yet. Activate your protection from Home; once active, the key details appear here.",
        overdueWarning:
          "We recommend renewing the key — it has been {{days}} days since the last renewal.",
        activeKeyTitle: "Active trust seal",
        fieldIdentifier: "Identifier",
        fieldLastRenewal: "Last renewal",
        fieldDaysActive: "Days active",
        fieldDaysActiveSuffix: "days",
        retiredKeysTitle: "Previous seals (still valid)",
        retiredKeysAriaLabel: "Previous seals still valid",
        retiredColIdentifier: "Identifier",
      },
      trustReceiptDetail: {
        ariaLabel: "Trust Receipt detail",
        title: "Receipt detail",
        closeAriaLabel: "Close panel",
        fieldReceiptId: "Receipt ID",
        fieldBucket: "Bucket",
        fieldTool: "Tool",
        fieldAgentId: "Agent ID",
        fieldSigningKey: "Signing Key (kid)",
        fieldInputHash: "Input Hash",
        fieldOutputHash: "Output Hash",
        fieldDate: "Date",
        showJws: "View full JWS",
        hideJws: "Hide full JWS",
        copyJwksUrl: "Copy JWKS URL",
        copied: "Copied!",
        closeBtn: "Close",
        downloadBundle: "Download receipt (ZIP)",
        downloading: "Preparing…",
        downloadHint:
          "Verifiable integrity proof — not, on its own, dispute evidence for your bank or PSP.",
        downloadErrorV10: "Only available for v1.1 receipts.",
        downloadErrorLegacy: "This receipt is not offline-exportable.",
        downloadErrorGeneric: "Could not download the receipt.",
      },
      trustReceiptsList: {
        description:
          "🤖 Purchases made by buying agents. When an automated buying agent enters your store, a signed receipt is kept here — like a till receipt, but for automated sales. Click on a row to see all the details of that sale.",
        pacoBenefits:
          "✅ What's in it for you? If a shopping agent claims they never placed that order, this receipt — tamper-evident and independently verifiable — is solid backup: you can download it and hand it to your bank or solicitor. On its own it does not replace the evidence they may require in a dispute. We store it for 5–10 years as required by law in your country — nothing for you to do.",
        pacoRegulation:
          "🏛️ Technical level: your receipts are designed to follow the electronic-signature standards of the EU (eIDAS), the United States (ESIGN) and the United Kingdom (UK DIATF), carrying a digital signature plus a timestamp. They are not yet a qualified signature or timestamp (that requires a certificate authority on the EU trusted list): they serve as a verifiable integrity proof you can submit as backup, not ready-made dispute evidence for a bank or court. We store them for 5–10 years as required by your country's law, automatically.",
        learnMore: "Learn how it works and what it's for",
        filterBuyerId: "Buyer ID",
        filterBuyerIdAria: "Filter by buyer ID",
        filterTypeTodos: "All types",
        filterTypeDiscovery: "Search",
        filterTypeCustomer: "Query",
        filterTypeCheckout: "Purchase",
        filterTypeAria: "Filter by type",
        filterDateFrom: "Date from",
        filterDateTo: "Date to",
        errorLoading: "Could not load sales:",
        tableLabel: "Sales to shopping agents list",
        colNumber: "No.",
        colOperation: "Operation",
        colType: "Type",
        colBuyer: "Buyer",
        colDate: "Date",
        noEntries: "No sales for the selected filters.",
        loadMore: "Load more",
        bucketDiscovery: "Search",
        bucketCustomer: "Query",
        bucketCheckout: "Purchase",
      },
      merchantAgentsPanel: {
        title: "Shopping Agents",
        description:
          "Here you can see the shopping agents that have already tried to purchase in your store. You can ban them, put them under surveillance, or let them buy freely.",
        errorLoading: "Could not load agents:",
        countersCanBuy: "Can buy",
        countersWatching: "Watching",
        countersBanned: "Banned",
        tableLabel: "Shopping agents list",
        colWho: "Who",
        colStatus: "Status",
        colReputation: "Reputation",
        colActivity: "Activity",
        colActions: "Action",
        emptyTitle: "No shopping agents connected yet",
        emptyDesc:
          "When an agent wants to buy from your store, they will appear here.",
        defaultAgentName: "Shopping agent",
        callsLast30d: "{{count}} purchases",
        callsLast30dSuffix: "last 30 days",
        stateCanBuy: "Can buy",
        stateWatching: "Under surveillance",
        stateBanned: "Banned",
        statePending: "Pending review",
        noData: "No data",
        trustVeryReliable: "Very reliable",
        trustAcceptable: "Acceptable — watch them",
        trustLow: "Low trust — be careful",
        revokeBtn: "🚫 Ban — cannot buy",
        probationBtn: "👁️ Watch — I'll review them",
        allowBtn: "✅ Allow to buy",
        closeNotification: "Close notification",
        toastRevoked: "Agent banned",
        toastProbation: "Agent under surveillance",
        toastAllowed: "Agent can buy",
        toastError: "Error updating agent",
        auditReason: "Changed from merchant admin ({{action}})",
        guideTitle: "How do I know if a shopping agent is trustworthy?",
        guide80: "80 - 100 points:",
        guide80Desc:
          "Very reliable. Has been buying without issues for a while.",
        guide50: "50 - 79 points:",
        guide50Desc:
          "Acceptable but watch them. They may have had minor incidents.",
        guide0: "0 - 49 points:",
        guide0Desc:
          "Low trust. Trusteed recommends banning them or putting them under surveillance.",
        notifications: "Notifications",
      },
      merchantCertNlweb: {
        errorCert: "Error loading certification.",
        errorNlweb: "Error loading NLWeb status.",
        errorWebmcp: "Error loading WebMCP status.",
        certTitle: "Certification",
        nlwebTitle: "NLWeb",
        webmcpTitle: "WebMCP",
        nlwebStatus: "Status",
        nlwebLastIndex: "Last indexed",
        nlwebProductsIndexed: "Indexed products",
        nlwebReindex: "Re-index",
        nlwebReindexing: "Starting...",
        nlwebReindexSuccess: "Re-indexing started",
        nlwebReindexError: "Error starting re-indexing",
        webmcpStatus: "Status",
        webmcpEnabled: "Enabled",
        webmcpDisabled: "Disabled",
        webmcpAdapter: "Detected adapter",
        webmcpLastConnection: "Last connection",
        notifications: "Notifications",
        closeNotification: "Close notification",
        certStatusLabels: {
          active: "Active",
          expiring_soon: "Expiring soon",
          expired: "Expired",
          pending: "Pending",
          not_applicable: "Not applicable",
        },
        nlwebStatusLabels: {
          idle: "Idle",
          indexing: "Indexing",
          error: "Error",
          never_indexed: "Never indexed",
        },
      },
      merchantCheckoutConfig: {
        description:
          "Configuration of payment methods enabled for agents at checkout.",
        ariaLoading: "Loading configuration",
        errorLoading: "Could not load configuration:",
        railsAriaLabel: "Checkout rails",
        enabled: "Enabled",
        disabled: "Disabled",
        noRails:
          "No payment rails configured yet. Connect a payment method to enable agent checkout.",
      },
      merchantOrders: {
        errorLoading: "Could not load orders:",
        tableLabel: "Orders list",
        colPlatform: "Platform",
        colOrderId: "Order ID",
        colStatus: "Status",
        colTotal: "Total",
        colDate: "Date",
        noOrders: "No orders available.",
        loadMore: "Load more",
      },
      merchantPaymentMethods: {
        description:
          "Connect the payment methods you accept from shopping agents. You can enable, disable or disconnect them at any time.",
        methodDescriptions: {
          stripe:
            "Global card processor. Accepts Visa, Mastercard, Amex and local methods. Connect with your Secret Key.",
          x402: "USDC payments over Base/Solana via the x402 protocol. On-chain settlement in seconds.",
          acp: "Agent-delegated payments via ACP (powered by Stripe). For agent-initiated checkout.",
          paypal: "Accept PayPal payments. Coming soon.",
          visa_vic:
            "Accept Visa payments via verified digital credentials. Coming soon.",
          mastercard_mcap:
            "Accept Mastercard payments via AI agents. Coming soon.",
          kyapay:
            "Buy now, pay later. Support for Europe (eurozone). Coming soon.",
        },
        errorLoading: "Could not load payment methods:",
        sectionAvailable: "Available now",
        sectionComingSoon: "Coming soon",
        noMethods: "No payment methods available at this time.",
        comingSoonLabel: "Coming soon",
        statusConnected: "Connected",
        statusVerifying: "Verifying",
        statusError: "Error",
        statusDisabled: "Disabled",
        statusAvailable: "Not connected",
        connectApiKey: "Secret API key",
        connectWallet: "Wallet address",
        connectBtn: "Connect",
        connecting: "Connecting…",
        enableBtn: "✅ Enable",
        disableBtn: "Disable",
        disconnectBtn: "🔌 Disconnect",
        actionConnected: "Connected",
        actionReconnected: "Reconnected",
        actionDisconnected: "Disconnected",
        actionEnabled: "Enabled",
        actionDisabled: "Disabled",
        actionError: "Error",
        verifiedAt: "Verified: {{date}}",
        closeBtn: "Close",
        notifications: "Notifications",
      },
      shopSwitcher: {
        superAdmin: "Super-admin",
        superAdminTitle: "Super-administrator — access to all stores",
        changeSrOnly: "Change store",
        shopLabel: "Store #{{id}}",
      },
      operatorsInstallations: {
        title: "Operator agents",
        description:
          "Operator agents act on this store on your behalf. Every privileged action is signed by your store key and logged in Trust Center.",
        errorLoading: "Failed to load installations: ",
        errorUnknown: "Unknown error",
        loadingAria: "Loading installations",
        tableLabel: "Operator installations",
        colVendorApp: "Vendor / App",
        colVersion: "Version",
        colModelHash: "Model hash",
        colPlatform: "Platform",
        colStatus: "Status",
        colInstalled: "Installed",
        colLastAction: "Last action",
        colReputation: "Reputation (V/A/I)",
        noInstallations:
          "No operator agents installed. Visit Trust Center to add.",
        justNow: "just now",
      },
      support: {
        buttonTitle: "Report a problem",
        modalTitle: "Something not working?",
        description:
          "Tell us what happened in your own words. We will automatically attach technical info so we can help you faster.",
        placeholder: "E.g. The 'My sales' page stays blank after loading…",
        autoLogsNote:
          "The last panel errors will be attached automatically (no customer data included).",
        send: "Send",
        sending: "Sending…",
        successTitle: "Ticket sent!",
        ticketLabel: "Ticket number:",
        successHint:
          "You will receive a reply to the email linked to your account. We usually reply within 24 hours.",
      },
    },
    Vb = { es: qb, en: Hh };
  function Qh() {
    try {
      const e = window;
      return (
        e.__AMCP_CONFIG__?.locale ??
        e.__AMCP_PS_CONFIG__?.locale ??
        e.__AMCP_MAGENTO_CONFIG__?.locale ??
        e.__AMCP_ODOO_CONFIG__?.locale ??
        navigator.language ??
        "en"
      )
        .split(/[-_]/)[0]
        .toLowerCase() === "es"
        ? "es"
        : "en";
    } catch {
      return "en";
    }
  }
  const Gh = R.createContext(Hh);
  function Kb({ children: e, locale: t }) {
    const r = t ?? Qh(),
      n = R.useMemo(() => Vb[r], [r]);
    return i.jsx(Gh.Provider, { value: n, children: e });
  }
  function Re() {
    return R.useContext(Gh);
  }
  function Wb() {
    return Qh();
  }
  function C(e, t, r) {
    function n(l, c) {
      if (
        (l._zod ||
          Object.defineProperty(l, "_zod", {
            value: { def: c, constr: o, traits: new Set() },
            enumerable: !1,
          }),
        l._zod.traits.has(e))
      )
        return;
      (l._zod.traits.add(e), t(l, c));
      const u = o.prototype,
        d = Object.keys(u);
      for (let f = 0; f < d.length; f++) {
        const m = d[f];
        m in l || (l[m] = u[m].bind(l));
      }
    }
    const s = r?.Parent ?? Object;
    class a extends s {}
    Object.defineProperty(a, "name", { value: e });
    function o(l) {
      var c;
      const u = r?.Parent ? new a() : this;
      (n(u, l), (c = u._zod).deferred ?? (c.deferred = []));
      for (const d of u._zod.deferred) d();
      return u;
    }
    return (
      Object.defineProperty(o, "init", { value: n }),
      Object.defineProperty(o, Symbol.hasInstance, {
        value: (l) =>
          r?.Parent && l instanceof r.Parent ? !0 : l?._zod?.traits?.has(e),
      }),
      Object.defineProperty(o, "name", { value: e }),
      o
    );
  }
  class js extends Error {
    constructor() {
      super(
        "Encountered Promise during synchronous parse. Use .parseAsync() instead."
      );
    }
  }
  class Yh extends Error {
    constructor(t) {
      (super(`Encountered unidirectional transform during encode: ${t}`),
        (this.name = "ZodEncodeError"));
    }
  }
  const Jh = {};
  function Hr(e) {
    return Jh;
  }
  function Xh(e) {
    const t = Object.values(e).filter((n) => typeof n == "number");
    return Object.entries(e)
      .filter(([n, s]) => t.indexOf(+n) === -1)
      .map(([n, s]) => s);
  }
  function Mu(e, t) {
    return typeof t == "bigint" ? t.toString() : t;
  }
  function $u(e) {
    return {
      get value() {
        {
          const t = e();
          return (Object.defineProperty(this, "value", { value: t }), t);
        }
      },
    };
  }
  function Fu(e) {
    return e == null;
  }
  function Uu(e) {
    const t = e.startsWith("^") ? 1 : 0,
      r = e.endsWith("$") ? e.length - 1 : e.length;
    return e.slice(t, r);
  }
  function Hb(e, t) {
    const r = (e.toString().split(".")[1] || "").length,
      n = t.toString();
    let s = (n.split(".")[1] || "").length;
    if (s === 0 && /\d?e-\d?/.test(n)) {
      const c = n.match(/\d?e-(\d?)/);
      c?.[1] && (s = Number.parseInt(c[1]));
    }
    const a = r > s ? r : s,
      o = Number.parseInt(e.toFixed(a).replace(".", "")),
      l = Number.parseInt(t.toFixed(a).replace(".", ""));
    return (o % l) / 10 ** a;
  }
  const eg = Symbol("evaluating");
  function fe(e, t, r) {
    let n;
    Object.defineProperty(e, t, {
      get() {
        if (n !== eg) return (n === void 0 && ((n = eg), (n = r())), n);
      },
      set(s) {
        Object.defineProperty(e, t, { value: s });
      },
      configurable: !0,
    });
  }
  function An(e, t, r) {
    Object.defineProperty(e, t, {
      value: r,
      writable: !0,
      enumerable: !0,
      configurable: !0,
    });
  }
  function En(...e) {
    const t = {};
    for (const r of e) {
      const n = Object.getOwnPropertyDescriptors(r);
      Object.assign(t, n);
    }
    return Object.defineProperties({}, t);
  }
  function tg(e) {
    return JSON.stringify(e);
  }
  function Qb(e) {
    return e
      .toLowerCase()
      .trim()
      .replace(/[^\w\s-]/g, "")
      .replace(/[\s_-]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }
  const rg =
    "captureStackTrace" in Error ? Error.captureStackTrace : (...e) => {};
  function Ti(e) {
    return typeof e == "object" && e !== null && !Array.isArray(e);
  }
  const Gb = $u(() => {
    if (typeof navigator < "u" && navigator?.userAgent?.includes("Cloudflare"))
      return !1;
    try {
      const e = Function;
      return (new e(""), !0);
    } catch {
      return !1;
    }
  });
  function Ns(e) {
    if (Ti(e) === !1) return !1;
    const t = e.constructor;
    if (t === void 0 || typeof t != "function") return !0;
    const r = t.prototype;
    return !(
      Ti(r) === !1 ||
      Object.prototype.hasOwnProperty.call(r, "isPrototypeOf") === !1
    );
  }
  function ng(e) {
    return Ns(e) ? { ...e } : Array.isArray(e) ? [...e] : e;
  }
  const Yb = new Set(["string", "number", "symbol"]);
  function Pi(e) {
    return e.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
  }
  function Qr(e, t, r) {
    const n = new e._zod.constr(t ?? e._zod.def);
    return ((!t || r?.parent) && (n._zod.parent = e), n);
  }
  function B(e) {
    const t = e;
    if (!t) return {};
    if (typeof t == "string") return { error: () => t };
    if (t?.message !== void 0) {
      if (t?.error !== void 0)
        throw new Error("Cannot specify both `message` and `error` params");
      t.error = t.message;
    }
    return (
      delete t.message,
      typeof t.error == "string" ? { ...t, error: () => t.error } : t
    );
  }
  function Jb(e) {
    return Object.keys(e).filter(
      (t) => e[t]._zod.optin === "optional" && e[t]._zod.optout === "optional"
    );
  }
  const Xb = {
    safeint: [Number.MIN_SAFE_INTEGER, Number.MAX_SAFE_INTEGER],
    int32: [-2147483648, 2147483647],
    uint32: [0, 4294967295],
    float32: [-34028234663852886e22, 34028234663852886e22],
    float64: [-Number.MAX_VALUE, Number.MAX_VALUE],
  };
  function ew(e, t) {
    const r = e._zod.def,
      n = En(e._zod.def, {
        get shape() {
          const s = {};
          for (const a in t) {
            if (!(a in r.shape)) throw new Error(`Unrecognized key: "${a}"`);
            t[a] && (s[a] = r.shape[a]);
          }
          return (An(this, "shape", s), s);
        },
        checks: [],
      });
    return Qr(e, n);
  }
  function tw(e, t) {
    const r = e._zod.def,
      n = En(e._zod.def, {
        get shape() {
          const s = { ...e._zod.def.shape };
          for (const a in t) {
            if (!(a in r.shape)) throw new Error(`Unrecognized key: "${a}"`);
            t[a] && delete s[a];
          }
          return (An(this, "shape", s), s);
        },
        checks: [],
      });
    return Qr(e, n);
  }
  function rw(e, t) {
    if (!Ns(t))
      throw new Error("Invalid input to extend: expected a plain object");
    const r = e._zod.def.checks;
    if (r && r.length > 0)
      throw new Error(
        "Object schemas containing refinements cannot be extended. Use `.safeExtend()` instead."
      );
    const s = En(e._zod.def, {
      get shape() {
        const a = { ...e._zod.def.shape, ...t };
        return (An(this, "shape", a), a);
      },
      checks: [],
    });
    return Qr(e, s);
  }
  function nw(e, t) {
    if (!Ns(t))
      throw new Error("Invalid input to safeExtend: expected a plain object");
    const r = {
      ...e._zod.def,
      get shape() {
        const n = { ...e._zod.def.shape, ...t };
        return (An(this, "shape", n), n);
      },
      checks: e._zod.def.checks,
    };
    return Qr(e, r);
  }
  function sw(e, t) {
    const r = En(e._zod.def, {
      get shape() {
        const n = { ...e._zod.def.shape, ...t._zod.def.shape };
        return (An(this, "shape", n), n);
      },
      get catchall() {
        return t._zod.def.catchall;
      },
      checks: [],
    });
    return Qr(e, r);
  }
  function aw(e, t, r) {
    const n = En(t._zod.def, {
      get shape() {
        const s = t._zod.def.shape,
          a = { ...s };
        if (r)
          for (const o in r) {
            if (!(o in s)) throw new Error(`Unrecognized key: "${o}"`);
            r[o] &&
              (a[o] = e ? new e({ type: "optional", innerType: s[o] }) : s[o]);
          }
        else
          for (const o in s)
            a[o] = e ? new e({ type: "optional", innerType: s[o] }) : s[o];
        return (An(this, "shape", a), a);
      },
      checks: [],
    });
    return Qr(t, n);
  }
  function ow(e, t, r) {
    const n = En(t._zod.def, {
      get shape() {
        const s = t._zod.def.shape,
          a = { ...s };
        if (r)
          for (const o in r) {
            if (!(o in a)) throw new Error(`Unrecognized key: "${o}"`);
            r[o] && (a[o] = new e({ type: "nonoptional", innerType: s[o] }));
          }
        else
          for (const o in s)
            a[o] = new e({ type: "nonoptional", innerType: s[o] });
        return (An(this, "shape", a), a);
      },
      checks: [],
    });
    return Qr(t, n);
  }
  function Rs(e, t = 0) {
    if (e.aborted === !0) return !0;
    for (let r = t; r < e.issues.length; r++)
      if (e.issues[r]?.continue !== !0) return !0;
    return !1;
  }
  function As(e, t) {
    return t.map((r) => {
      var n;
      return ((n = r).path ?? (n.path = []), r.path.unshift(e), r);
    });
  }
  function Oi(e) {
    return typeof e == "string" ? e : e?.message;
  }
  function Gr(e, t, r) {
    const n = { ...e, path: e.path ?? [] };
    if (!e.message) {
      const s =
        Oi(e.inst?._zod.def?.error?.(e)) ??
        Oi(t?.error?.(e)) ??
        Oi(r.customError?.(e)) ??
        Oi(r.localeError?.(e)) ??
        "Invalid input";
      n.message = s;
    }
    return (
      delete n.inst,
      delete n.continue,
      t?.reportInput || delete n.input,
      n
    );
  }
  function Zu(e) {
    return Array.isArray(e)
      ? "array"
      : typeof e == "string"
        ? "string"
        : "unknown";
  }
  function Ua(...e) {
    const [t, r, n] = e;
    return typeof t == "string"
      ? { message: t, code: "custom", input: r, inst: n }
      : { ...t };
  }
  const sg = (e, t) => {
      ((e.name = "$ZodError"),
        Object.defineProperty(e, "_zod", { value: e._zod, enumerable: !1 }),
        Object.defineProperty(e, "issues", { value: t, enumerable: !1 }),
        (e.message = JSON.stringify(t, Mu, 2)),
        Object.defineProperty(e, "toString", {
          value: () => e.message,
          enumerable: !1,
        }));
    },
    ag = C("$ZodError", sg),
    og = C("$ZodError", sg, { Parent: Error });
  function iw(e, t = (r) => r.message) {
    const r = {},
      n = [];
    for (const s of e.issues)
      s.path.length > 0
        ? ((r[s.path[0]] = r[s.path[0]] || []), r[s.path[0]].push(t(s)))
        : n.push(t(s));
    return { formErrors: n, fieldErrors: r };
  }
  function lw(e, t = (r) => r.message) {
    const r = { _errors: [] },
      n = (s) => {
        for (const a of s.issues)
          if (a.code === "invalid_union" && a.errors.length)
            a.errors.map((o) => n({ issues: o }));
          else if (a.code === "invalid_key") n({ issues: a.issues });
          else if (a.code === "invalid_element") n({ issues: a.issues });
          else if (a.path.length === 0) r._errors.push(t(a));
          else {
            let o = r,
              l = 0;
            for (; l < a.path.length; ) {
              const c = a.path[l];
              (l === a.path.length - 1
                ? ((o[c] = o[c] || { _errors: [] }), o[c]._errors.push(t(a)))
                : (o[c] = o[c] || { _errors: [] }),
                (o = o[c]),
                l++);
            }
          }
      };
    return (n(e), r);
  }
  const Bu = (e) => (t, r, n, s) => {
      const a = n ? Object.assign(n, { async: !1 }) : { async: !1 },
        o = t._zod.run({ value: r, issues: [] }, a);
      if (o instanceof Promise) throw new js();
      if (o.issues.length) {
        const l = new (s?.Err ?? e)(o.issues.map((c) => Gr(c, a, Hr())));
        throw (rg(l, s?.callee), l);
      }
      return o.value;
    },
    qu = (e) => async (t, r, n, s) => {
      const a = n ? Object.assign(n, { async: !0 }) : { async: !0 };
      let o = t._zod.run({ value: r, issues: [] }, a);
      if ((o instanceof Promise && (o = await o), o.issues.length)) {
        const l = new (s?.Err ?? e)(o.issues.map((c) => Gr(c, a, Hr())));
        throw (rg(l, s?.callee), l);
      }
      return o.value;
    },
    Ii = (e) => (t, r, n) => {
      const s = n ? { ...n, async: !1 } : { async: !1 },
        a = t._zod.run({ value: r, issues: [] }, s);
      if (a instanceof Promise) throw new js();
      return a.issues.length
        ? {
            success: !1,
            error: new (e ?? ag)(a.issues.map((o) => Gr(o, s, Hr()))),
          }
        : { success: !0, data: a.value };
    },
    cw = Ii(og),
    Li = (e) => async (t, r, n) => {
      const s = n ? Object.assign(n, { async: !0 }) : { async: !0 };
      let a = t._zod.run({ value: r, issues: [] }, s);
      return (
        a instanceof Promise && (a = await a),
        a.issues.length
          ? { success: !1, error: new e(a.issues.map((o) => Gr(o, s, Hr()))) }
          : { success: !0, data: a.value }
      );
    },
    uw = Li(og),
    dw = (e) => (t, r, n) => {
      const s = n
        ? Object.assign(n, { direction: "backward" })
        : { direction: "backward" };
      return Bu(e)(t, r, s);
    },
    pw = (e) => (t, r, n) => Bu(e)(t, r, n),
    mw = (e) => async (t, r, n) => {
      const s = n
        ? Object.assign(n, { direction: "backward" })
        : { direction: "backward" };
      return qu(e)(t, r, s);
    },
    fw = (e) => async (t, r, n) => qu(e)(t, r, n),
    hw = (e) => (t, r, n) => {
      const s = n
        ? Object.assign(n, { direction: "backward" })
        : { direction: "backward" };
      return Ii(e)(t, r, s);
    },
    gw = (e) => (t, r, n) => Ii(e)(t, r, n),
    yw = (e) => async (t, r, n) => {
      const s = n
        ? Object.assign(n, { direction: "backward" })
        : { direction: "backward" };
      return Li(e)(t, r, s);
    },
    vw = (e) => async (t, r, n) => Li(e)(t, r, n),
    xw = /^[cC][^\s-]{8,}$/,
    bw = /^[0-9a-z]+$/,
    ww = /^[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}$/,
    kw = /^[0-9a-vA-V]{20}$/,
    _w = /^[A-Za-z0-9]{27}$/,
    Sw = /^[a-zA-Z0-9_-]{21}$/,
    Cw =
      /^P(?:(\d+W)|(?!.*W)(?=\d|T\d)(\d+Y)?(\d+M)?(\d+D)?(T(?=\d)(\d+H)?(\d+M)?(\d+([.,]\d+)?S)?)?)$/,
    jw =
      /^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})$/,
    ig = (e) =>
      e
        ? new RegExp(
            `^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-${e}[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12})$`
          )
        : /^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}|00000000-0000-0000-0000-000000000000|ffffffff-ffff-ffff-ffff-ffffffffffff)$/,
    Nw =
      /^(?!\.)(?!.*\.\.)([A-Za-z0-9_'+\-\.]*)[A-Za-z0-9_+-]@([A-Za-z0-9][A-Za-z0-9\-]*\.)+[A-Za-z]{2,}$/,
    Rw = "^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$";
  function Aw() {
    return new RegExp(Rw, "u");
  }
  const Ew =
      /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/,
    Tw =
      /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:))$/,
    Pw =
      /^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/([0-9]|[1-2][0-9]|3[0-2])$/,
    Ow =
      /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::|([0-9a-fA-F]{1,4})?::([0-9a-fA-F]{1,4}:?){0,6})\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/,
    Iw =
      /^$|^(?:[0-9a-zA-Z+/]{4})*(?:(?:[0-9a-zA-Z+/]{2}==)|(?:[0-9a-zA-Z+/]{3}=))?$/,
    lg = /^[A-Za-z0-9_-]*$/,
    Lw = /^\+(?:[0-9]){6,14}[0-9]$/,
    cg =
      "(?:(?:\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-(?:(?:0[13578]|1[02])-(?:0[1-9]|[12]\\d|3[01])|(?:0[469]|11)-(?:0[1-9]|[12]\\d|30)|(?:02)-(?:0[1-9]|1\\d|2[0-8])))",
    Dw = new RegExp(`^${cg}$`);
  function ug(e) {
    const t = "(?:[01]\\d|2[0-3]):[0-5]\\d";
    return typeof e.precision == "number"
      ? e.precision === -1
        ? `${t}`
        : e.precision === 0
          ? `${t}:[0-5]\\d`
          : `${t}:[0-5]\\d\\.\\d{${e.precision}}`
      : `${t}(?::[0-5]\\d(?:\\.\\d+)?)?`;
  }
  function zw(e) {
    return new RegExp(`^${ug(e)}$`);
  }
  function Mw(e) {
    const t = ug({ precision: e.precision }),
      r = ["Z"];
    (e.local && r.push(""),
      e.offset && r.push("([+-](?:[01]\\d|2[0-3]):[0-5]\\d)"));
    const n = `${t}(?:${r.join("|")})`;
    return new RegExp(`^${cg}T(?:${n})$`);
  }
  const $w = (e) => {
      const t = e
        ? `[\\s\\S]{${e?.minimum ?? 0},${e?.maximum ?? ""}}`
        : "[\\s\\S]*";
      return new RegExp(`^${t}$`);
    },
    Fw = /^-?\d+$/,
    Uw = /^-?\d+(?:\.\d+)?/,
    Zw = /^(?:true|false)$/i,
    Bw = /^[^A-Z]*$/,
    qw = /^[^a-z]*$/,
    mt = C("$ZodCheck", (e, t) => {
      var r;
      (e._zod ?? (e._zod = {}),
        (e._zod.def = t),
        (r = e._zod).onattach ?? (r.onattach = []));
    }),
    dg = { number: "number", bigint: "bigint", object: "date" },
    pg = C("$ZodCheckLessThan", (e, t) => {
      mt.init(e, t);
      const r = dg[typeof t.value];
      (e._zod.onattach.push((n) => {
        const s = n._zod.bag,
          a =
            (t.inclusive ? s.maximum : s.exclusiveMaximum) ??
            Number.POSITIVE_INFINITY;
        t.value < a &&
          (t.inclusive
            ? (s.maximum = t.value)
            : (s.exclusiveMaximum = t.value));
      }),
        (e._zod.check = (n) => {
          (t.inclusive ? n.value <= t.value : n.value < t.value) ||
            n.issues.push({
              origin: r,
              code: "too_big",
              maximum: t.value,
              input: n.value,
              inclusive: t.inclusive,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    mg = C("$ZodCheckGreaterThan", (e, t) => {
      mt.init(e, t);
      const r = dg[typeof t.value];
      (e._zod.onattach.push((n) => {
        const s = n._zod.bag,
          a =
            (t.inclusive ? s.minimum : s.exclusiveMinimum) ??
            Number.NEGATIVE_INFINITY;
        t.value > a &&
          (t.inclusive
            ? (s.minimum = t.value)
            : (s.exclusiveMinimum = t.value));
      }),
        (e._zod.check = (n) => {
          (t.inclusive ? n.value >= t.value : n.value > t.value) ||
            n.issues.push({
              origin: r,
              code: "too_small",
              minimum: t.value,
              input: n.value,
              inclusive: t.inclusive,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    Vw = C("$ZodCheckMultipleOf", (e, t) => {
      (mt.init(e, t),
        e._zod.onattach.push((r) => {
          var n;
          (n = r._zod.bag).multipleOf ?? (n.multipleOf = t.value);
        }),
        (e._zod.check = (r) => {
          if (typeof r.value != typeof t.value)
            throw new Error(
              "Cannot mix number and bigint in multiple_of check."
            );
          (typeof r.value == "bigint"
            ? r.value % t.value === BigInt(0)
            : Hb(r.value, t.value) === 0) ||
            r.issues.push({
              origin: typeof r.value,
              code: "not_multiple_of",
              divisor: t.value,
              input: r.value,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    Kw = C("$ZodCheckNumberFormat", (e, t) => {
      (mt.init(e, t), (t.format = t.format || "float64"));
      const r = t.format?.includes("int"),
        n = r ? "int" : "number",
        [s, a] = Xb[t.format];
      (e._zod.onattach.push((o) => {
        const l = o._zod.bag;
        ((l.format = t.format),
          (l.minimum = s),
          (l.maximum = a),
          r && (l.pattern = Fw));
      }),
        (e._zod.check = (o) => {
          const l = o.value;
          if (r) {
            if (!Number.isInteger(l)) {
              o.issues.push({
                expected: n,
                format: t.format,
                code: "invalid_type",
                continue: !1,
                input: l,
                inst: e,
              });
              return;
            }
            if (!Number.isSafeInteger(l)) {
              l > 0
                ? o.issues.push({
                    input: l,
                    code: "too_big",
                    maximum: Number.MAX_SAFE_INTEGER,
                    note: "Integers must be within the safe integer range.",
                    inst: e,
                    origin: n,
                    continue: !t.abort,
                  })
                : o.issues.push({
                    input: l,
                    code: "too_small",
                    minimum: Number.MIN_SAFE_INTEGER,
                    note: "Integers must be within the safe integer range.",
                    inst: e,
                    origin: n,
                    continue: !t.abort,
                  });
              return;
            }
          }
          (l < s &&
            o.issues.push({
              origin: "number",
              input: l,
              code: "too_small",
              minimum: s,
              inclusive: !0,
              inst: e,
              continue: !t.abort,
            }),
            l > a &&
              o.issues.push({
                origin: "number",
                input: l,
                code: "too_big",
                maximum: a,
                inst: e,
              }));
        }));
    }),
    Ww = C("$ZodCheckMaxLength", (e, t) => {
      var r;
      (mt.init(e, t),
        (r = e._zod.def).when ??
          (r.when = (n) => {
            const s = n.value;
            return !Fu(s) && s.length !== void 0;
          }),
        e._zod.onattach.push((n) => {
          const s = n._zod.bag.maximum ?? Number.POSITIVE_INFINITY;
          t.maximum < s && (n._zod.bag.maximum = t.maximum);
        }),
        (e._zod.check = (n) => {
          const s = n.value;
          if (s.length <= t.maximum) return;
          const o = Zu(s);
          n.issues.push({
            origin: o,
            code: "too_big",
            maximum: t.maximum,
            inclusive: !0,
            input: s,
            inst: e,
            continue: !t.abort,
          });
        }));
    }),
    Hw = C("$ZodCheckMinLength", (e, t) => {
      var r;
      (mt.init(e, t),
        (r = e._zod.def).when ??
          (r.when = (n) => {
            const s = n.value;
            return !Fu(s) && s.length !== void 0;
          }),
        e._zod.onattach.push((n) => {
          const s = n._zod.bag.minimum ?? Number.NEGATIVE_INFINITY;
          t.minimum > s && (n._zod.bag.minimum = t.minimum);
        }),
        (e._zod.check = (n) => {
          const s = n.value;
          if (s.length >= t.minimum) return;
          const o = Zu(s);
          n.issues.push({
            origin: o,
            code: "too_small",
            minimum: t.minimum,
            inclusive: !0,
            input: s,
            inst: e,
            continue: !t.abort,
          });
        }));
    }),
    Qw = C("$ZodCheckLengthEquals", (e, t) => {
      var r;
      (mt.init(e, t),
        (r = e._zod.def).when ??
          (r.when = (n) => {
            const s = n.value;
            return !Fu(s) && s.length !== void 0;
          }),
        e._zod.onattach.push((n) => {
          const s = n._zod.bag;
          ((s.minimum = t.length),
            (s.maximum = t.length),
            (s.length = t.length));
        }),
        (e._zod.check = (n) => {
          const s = n.value,
            a = s.length;
          if (a === t.length) return;
          const o = Zu(s),
            l = a > t.length;
          n.issues.push({
            origin: o,
            ...(l
              ? { code: "too_big", maximum: t.length }
              : { code: "too_small", minimum: t.length }),
            inclusive: !0,
            exact: !0,
            input: n.value,
            inst: e,
            continue: !t.abort,
          });
        }));
    }),
    Di = C("$ZodCheckStringFormat", (e, t) => {
      var r, n;
      (mt.init(e, t),
        e._zod.onattach.push((s) => {
          const a = s._zod.bag;
          ((a.format = t.format),
            t.pattern &&
              (a.patterns ?? (a.patterns = new Set()),
              a.patterns.add(t.pattern)));
        }),
        t.pattern
          ? ((r = e._zod).check ??
            (r.check = (s) => {
              ((t.pattern.lastIndex = 0),
                !t.pattern.test(s.value) &&
                  s.issues.push({
                    origin: "string",
                    code: "invalid_format",
                    format: t.format,
                    input: s.value,
                    ...(t.pattern ? { pattern: t.pattern.toString() } : {}),
                    inst: e,
                    continue: !t.abort,
                  }));
            }))
          : ((n = e._zod).check ?? (n.check = () => {})));
    }),
    Gw = C("$ZodCheckRegex", (e, t) => {
      (Di.init(e, t),
        (e._zod.check = (r) => {
          ((t.pattern.lastIndex = 0),
            !t.pattern.test(r.value) &&
              r.issues.push({
                origin: "string",
                code: "invalid_format",
                format: "regex",
                input: r.value,
                pattern: t.pattern.toString(),
                inst: e,
                continue: !t.abort,
              }));
        }));
    }),
    Yw = C("$ZodCheckLowerCase", (e, t) => {
      (t.pattern ?? (t.pattern = Bw), Di.init(e, t));
    }),
    Jw = C("$ZodCheckUpperCase", (e, t) => {
      (t.pattern ?? (t.pattern = qw), Di.init(e, t));
    }),
    Xw = C("$ZodCheckIncludes", (e, t) => {
      mt.init(e, t);
      const r = Pi(t.includes),
        n = new RegExp(
          typeof t.position == "number" ? `^.{${t.position}}${r}` : r
        );
      ((t.pattern = n),
        e._zod.onattach.push((s) => {
          const a = s._zod.bag;
          (a.patterns ?? (a.patterns = new Set()), a.patterns.add(n));
        }),
        (e._zod.check = (s) => {
          s.value.includes(t.includes, t.position) ||
            s.issues.push({
              origin: "string",
              code: "invalid_format",
              format: "includes",
              includes: t.includes,
              input: s.value,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    e1 = C("$ZodCheckStartsWith", (e, t) => {
      mt.init(e, t);
      const r = new RegExp(`^${Pi(t.prefix)}.*`);
      (t.pattern ?? (t.pattern = r),
        e._zod.onattach.push((n) => {
          const s = n._zod.bag;
          (s.patterns ?? (s.patterns = new Set()), s.patterns.add(r));
        }),
        (e._zod.check = (n) => {
          n.value.startsWith(t.prefix) ||
            n.issues.push({
              origin: "string",
              code: "invalid_format",
              format: "starts_with",
              prefix: t.prefix,
              input: n.value,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    t1 = C("$ZodCheckEndsWith", (e, t) => {
      mt.init(e, t);
      const r = new RegExp(`.*${Pi(t.suffix)}$`);
      (t.pattern ?? (t.pattern = r),
        e._zod.onattach.push((n) => {
          const s = n._zod.bag;
          (s.patterns ?? (s.patterns = new Set()), s.patterns.add(r));
        }),
        (e._zod.check = (n) => {
          n.value.endsWith(t.suffix) ||
            n.issues.push({
              origin: "string",
              code: "invalid_format",
              format: "ends_with",
              suffix: t.suffix,
              input: n.value,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    r1 = C("$ZodCheckOverwrite", (e, t) => {
      (mt.init(e, t),
        (e._zod.check = (r) => {
          r.value = t.tx(r.value);
        }));
    });
  class n1 {
    constructor(t = []) {
      ((this.content = []), (this.indent = 0), this && (this.args = t));
    }
    indented(t) {
      ((this.indent += 1), t(this), (this.indent -= 1));
    }
    write(t) {
      if (typeof t == "function") {
        (t(this, { execution: "sync" }), t(this, { execution: "async" }));
        return;
      }
      const n = t
          .split(
            `
`
          )
          .filter((o) => o),
        s = Math.min(...n.map((o) => o.length - o.trimStart().length)),
        a = n
          .map((o) => o.slice(s))
          .map((o) => " ".repeat(this.indent * 2) + o);
      for (const o of a) this.content.push(o);
    }
    compile() {
      const t = Function,
        r = this?.args,
        s = [...(this?.content ?? [""]).map((a) => `  ${a}`)];
      return new t(
        ...r,
        s.join(`
`)
      );
    }
  }
  const s1 = { major: 4, minor: 2, patch: 1 },
    Se = C("$ZodType", (e, t) => {
      var r;
      (e ?? (e = {}),
        (e._zod.def = t),
        (e._zod.bag = e._zod.bag || {}),
        (e._zod.version = s1));
      const n = [...(e._zod.def.checks ?? [])];
      e._zod.traits.has("$ZodCheck") && n.unshift(e);
      for (const s of n) for (const a of s._zod.onattach) a(e);
      if (n.length === 0)
        ((r = e._zod).deferred ?? (r.deferred = []),
          e._zod.deferred?.push(() => {
            e._zod.run = e._zod.parse;
          }));
      else {
        const s = (o, l, c) => {
            let u = Rs(o),
              d;
            for (const f of l) {
              if (f._zod.def.when) {
                if (!f._zod.def.when(o)) continue;
              } else if (u) continue;
              const m = o.issues.length,
                v = f._zod.check(o);
              if (v instanceof Promise && c?.async === !1) throw new js();
              if (d || v instanceof Promise)
                d = (d ?? Promise.resolve()).then(async () => {
                  (await v, o.issues.length !== m && (u || (u = Rs(o, m))));
                });
              else {
                if (o.issues.length === m) continue;
                u || (u = Rs(o, m));
              }
            }
            return d ? d.then(() => o) : o;
          },
          a = (o, l, c) => {
            if (Rs(o)) return ((o.aborted = !0), o);
            const u = s(l, n, c);
            if (u instanceof Promise) {
              if (c.async === !1) throw new js();
              return u.then((d) => e._zod.parse(d, c));
            }
            return e._zod.parse(u, c);
          };
        e._zod.run = (o, l) => {
          if (l.skipChecks) return e._zod.parse(o, l);
          if (l.direction === "backward") {
            const u = e._zod.parse(
              { value: o.value, issues: [] },
              { ...l, skipChecks: !0 }
            );
            return u instanceof Promise
              ? u.then((d) => a(d, o, l))
              : a(u, o, l);
          }
          const c = e._zod.parse(o, l);
          if (c instanceof Promise) {
            if (l.async === !1) throw new js();
            return c.then((u) => s(u, n, l));
          }
          return s(c, n, l);
        };
      }
      e["~standard"] = {
        validate: (s) => {
          try {
            const a = cw(e, s);
            return a.success ? { value: a.data } : { issues: a.error?.issues };
          } catch {
            return uw(e, s).then((o) =>
              o.success ? { value: o.data } : { issues: o.error?.issues }
            );
          }
        },
        vendor: "zod",
        version: 1,
      };
    }),
    Vu = C("$ZodString", (e, t) => {
      (Se.init(e, t),
        (e._zod.pattern =
          [...(e?._zod.bag?.patterns ?? [])].pop() ?? $w(e._zod.bag)),
        (e._zod.parse = (r, n) => {
          if (t.coerce)
            try {
              r.value = String(r.value);
            } catch {}
          return (
            typeof r.value == "string" ||
              r.issues.push({
                expected: "string",
                code: "invalid_type",
                input: r.value,
                inst: e,
              }),
            r
          );
        }));
    }),
    xe = C("$ZodStringFormat", (e, t) => {
      (Di.init(e, t), Vu.init(e, t));
    }),
    a1 = C("$ZodGUID", (e, t) => {
      (t.pattern ?? (t.pattern = jw), xe.init(e, t));
    }),
    o1 = C("$ZodUUID", (e, t) => {
      if (t.version) {
        const n = { v1: 1, v2: 2, v3: 3, v4: 4, v5: 5, v6: 6, v7: 7, v8: 8 }[
          t.version
        ];
        if (n === void 0)
          throw new Error(`Invalid UUID version: "${t.version}"`);
        t.pattern ?? (t.pattern = ig(n));
      } else t.pattern ?? (t.pattern = ig());
      xe.init(e, t);
    }),
    i1 = C("$ZodEmail", (e, t) => {
      (t.pattern ?? (t.pattern = Nw), xe.init(e, t));
    }),
    l1 = C("$ZodURL", (e, t) => {
      (xe.init(e, t),
        (e._zod.check = (r) => {
          try {
            const n = r.value.trim(),
              s = new URL(n);
            (t.hostname &&
              ((t.hostname.lastIndex = 0),
              t.hostname.test(s.hostname) ||
                r.issues.push({
                  code: "invalid_format",
                  format: "url",
                  note: "Invalid hostname",
                  pattern: t.hostname.source,
                  input: r.value,
                  inst: e,
                  continue: !t.abort,
                })),
              t.protocol &&
                ((t.protocol.lastIndex = 0),
                t.protocol.test(
                  s.protocol.endsWith(":")
                    ? s.protocol.slice(0, -1)
                    : s.protocol
                ) ||
                  r.issues.push({
                    code: "invalid_format",
                    format: "url",
                    note: "Invalid protocol",
                    pattern: t.protocol.source,
                    input: r.value,
                    inst: e,
                    continue: !t.abort,
                  })),
              t.normalize ? (r.value = s.href) : (r.value = n));
            return;
          } catch {
            r.issues.push({
              code: "invalid_format",
              format: "url",
              input: r.value,
              inst: e,
              continue: !t.abort,
            });
          }
        }));
    }),
    c1 = C("$ZodEmoji", (e, t) => {
      (t.pattern ?? (t.pattern = Aw()), xe.init(e, t));
    }),
    u1 = C("$ZodNanoID", (e, t) => {
      (t.pattern ?? (t.pattern = Sw), xe.init(e, t));
    }),
    d1 = C("$ZodCUID", (e, t) => {
      (t.pattern ?? (t.pattern = xw), xe.init(e, t));
    }),
    p1 = C("$ZodCUID2", (e, t) => {
      (t.pattern ?? (t.pattern = bw), xe.init(e, t));
    }),
    m1 = C("$ZodULID", (e, t) => {
      (t.pattern ?? (t.pattern = ww), xe.init(e, t));
    }),
    f1 = C("$ZodXID", (e, t) => {
      (t.pattern ?? (t.pattern = kw), xe.init(e, t));
    }),
    h1 = C("$ZodKSUID", (e, t) => {
      (t.pattern ?? (t.pattern = _w), xe.init(e, t));
    }),
    g1 = C("$ZodISODateTime", (e, t) => {
      (t.pattern ?? (t.pattern = Mw(t)), xe.init(e, t));
    }),
    y1 = C("$ZodISODate", (e, t) => {
      (t.pattern ?? (t.pattern = Dw), xe.init(e, t));
    }),
    v1 = C("$ZodISOTime", (e, t) => {
      (t.pattern ?? (t.pattern = zw(t)), xe.init(e, t));
    }),
    x1 = C("$ZodISODuration", (e, t) => {
      (t.pattern ?? (t.pattern = Cw), xe.init(e, t));
    }),
    b1 = C("$ZodIPv4", (e, t) => {
      (t.pattern ?? (t.pattern = Ew),
        xe.init(e, t),
        (e._zod.bag.format = "ipv4"));
    }),
    w1 = C("$ZodIPv6", (e, t) => {
      (t.pattern ?? (t.pattern = Tw),
        xe.init(e, t),
        (e._zod.bag.format = "ipv6"),
        (e._zod.check = (r) => {
          try {
            new URL(`http://[${r.value}]`);
          } catch {
            r.issues.push({
              code: "invalid_format",
              format: "ipv6",
              input: r.value,
              inst: e,
              continue: !t.abort,
            });
          }
        }));
    }),
    k1 = C("$ZodCIDRv4", (e, t) => {
      (t.pattern ?? (t.pattern = Pw), xe.init(e, t));
    }),
    _1 = C("$ZodCIDRv6", (e, t) => {
      (t.pattern ?? (t.pattern = Ow),
        xe.init(e, t),
        (e._zod.check = (r) => {
          const n = r.value.split("/");
          try {
            if (n.length !== 2) throw new Error();
            const [s, a] = n;
            if (!a) throw new Error();
            const o = Number(a);
            if (`${o}` !== a) throw new Error();
            if (o < 0 || o > 128) throw new Error();
            new URL(`http://[${s}]`);
          } catch {
            r.issues.push({
              code: "invalid_format",
              format: "cidrv6",
              input: r.value,
              inst: e,
              continue: !t.abort,
            });
          }
        }));
    });
  function fg(e) {
    if (e === "") return !0;
    if (e.length % 4 !== 0) return !1;
    try {
      return (atob(e), !0);
    } catch {
      return !1;
    }
  }
  const S1 = C("$ZodBase64", (e, t) => {
    (t.pattern ?? (t.pattern = Iw),
      xe.init(e, t),
      (e._zod.bag.contentEncoding = "base64"),
      (e._zod.check = (r) => {
        fg(r.value) ||
          r.issues.push({
            code: "invalid_format",
            format: "base64",
            input: r.value,
            inst: e,
            continue: !t.abort,
          });
      }));
  });
  function C1(e) {
    if (!lg.test(e)) return !1;
    const t = e.replace(/[-_]/g, (n) => (n === "-" ? "+" : "/")),
      r = t.padEnd(Math.ceil(t.length / 4) * 4, "=");
    return fg(r);
  }
  const j1 = C("$ZodBase64URL", (e, t) => {
      (t.pattern ?? (t.pattern = lg),
        xe.init(e, t),
        (e._zod.bag.contentEncoding = "base64url"),
        (e._zod.check = (r) => {
          C1(r.value) ||
            r.issues.push({
              code: "invalid_format",
              format: "base64url",
              input: r.value,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    N1 = C("$ZodE164", (e, t) => {
      (t.pattern ?? (t.pattern = Lw), xe.init(e, t));
    });
  function R1(e, t = null) {
    try {
      const r = e.split(".");
      if (r.length !== 3) return !1;
      const [n] = r;
      if (!n) return !1;
      const s = JSON.parse(atob(n));
      return !(
        ("typ" in s && s?.typ !== "JWT") ||
        !s.alg ||
        (t && (!("alg" in s) || s.alg !== t))
      );
    } catch {
      return !1;
    }
  }
  const A1 = C("$ZodJWT", (e, t) => {
      (xe.init(e, t),
        (e._zod.check = (r) => {
          R1(r.value, t.alg) ||
            r.issues.push({
              code: "invalid_format",
              format: "jwt",
              input: r.value,
              inst: e,
              continue: !t.abort,
            });
        }));
    }),
    hg = C("$ZodNumber", (e, t) => {
      (Se.init(e, t),
        (e._zod.pattern = e._zod.bag.pattern ?? Uw),
        (e._zod.parse = (r, n) => {
          if (t.coerce)
            try {
              r.value = Number(r.value);
            } catch {}
          const s = r.value;
          if (typeof s == "number" && !Number.isNaN(s) && Number.isFinite(s))
            return r;
          const a =
            typeof s == "number"
              ? Number.isNaN(s)
                ? "NaN"
                : Number.isFinite(s)
                  ? void 0
                  : "Infinity"
              : void 0;
          return (
            r.issues.push({
              expected: "number",
              code: "invalid_type",
              input: s,
              inst: e,
              ...(a ? { received: a } : {}),
            }),
            r
          );
        }));
    }),
    E1 = C("$ZodNumberFormat", (e, t) => {
      (Kw.init(e, t), hg.init(e, t));
    }),
    T1 = C("$ZodBoolean", (e, t) => {
      (Se.init(e, t),
        (e._zod.pattern = Zw),
        (e._zod.parse = (r, n) => {
          if (t.coerce)
            try {
              r.value = !!r.value;
            } catch {}
          const s = r.value;
          return (
            typeof s == "boolean" ||
              r.issues.push({
                expected: "boolean",
                code: "invalid_type",
                input: s,
                inst: e,
              }),
            r
          );
        }));
    }),
    P1 = C("$ZodAny", (e, t) => {
      (Se.init(e, t), (e._zod.parse = (r) => r));
    }),
    O1 = C("$ZodUnknown", (e, t) => {
      (Se.init(e, t), (e._zod.parse = (r) => r));
    }),
    I1 = C("$ZodNever", (e, t) => {
      (Se.init(e, t),
        (e._zod.parse = (r, n) => (
          r.issues.push({
            expected: "never",
            code: "invalid_type",
            input: r.value,
            inst: e,
          }),
          r
        )));
    });
  function gg(e, t, r) {
    (e.issues.length && t.issues.push(...As(r, e.issues)),
      (t.value[r] = e.value));
  }
  const L1 = C("$ZodArray", (e, t) => {
    (Se.init(e, t),
      (e._zod.parse = (r, n) => {
        const s = r.value;
        if (!Array.isArray(s))
          return (
            r.issues.push({
              expected: "array",
              code: "invalid_type",
              input: s,
              inst: e,
            }),
            r
          );
        r.value = Array(s.length);
        const a = [];
        for (let o = 0; o < s.length; o++) {
          const l = s[o],
            c = t.element._zod.run({ value: l, issues: [] }, n);
          c instanceof Promise
            ? a.push(c.then((u) => gg(u, r, o)))
            : gg(c, r, o);
        }
        return a.length ? Promise.all(a).then(() => r) : r;
      }));
  });
  function zi(e, t, r, n) {
    (e.issues.length && t.issues.push(...As(r, e.issues)),
      e.value === void 0
        ? r in n && (t.value[r] = void 0)
        : (t.value[r] = e.value));
  }
  function yg(e) {
    const t = Object.keys(e.shape);
    for (const n of t)
      if (!e.shape?.[n]?._zod?.traits?.has("$ZodType"))
        throw new Error(`Invalid element at key "${n}": expected a Zod schema`);
    const r = Jb(e.shape);
    return {
      ...e,
      keys: t,
      keySet: new Set(t),
      numKeys: t.length,
      optionalKeys: new Set(r),
    };
  }
  function vg(e, t, r, n, s, a) {
    const o = [],
      l = s.keySet,
      c = s.catchall._zod,
      u = c.def.type;
    for (const d in t) {
      if (l.has(d)) continue;
      if (u === "never") {
        o.push(d);
        continue;
      }
      const f = c.run({ value: t[d], issues: [] }, n);
      f instanceof Promise
        ? e.push(f.then((m) => zi(m, r, d, t)))
        : zi(f, r, d, t);
    }
    return (
      o.length &&
        r.issues.push({
          code: "unrecognized_keys",
          keys: o,
          input: t,
          inst: a,
        }),
      e.length ? Promise.all(e).then(() => r) : r
    );
  }
  const D1 = C("$ZodObject", (e, t) => {
      if ((Se.init(e, t), !Object.getOwnPropertyDescriptor(t, "shape")?.get)) {
        const l = t.shape;
        Object.defineProperty(t, "shape", {
          get: () => {
            const c = { ...l };
            return (Object.defineProperty(t, "shape", { value: c }), c);
          },
        });
      }
      const n = $u(() => yg(t));
      fe(e._zod, "propValues", () => {
        const l = t.shape,
          c = {};
        for (const u in l) {
          const d = l[u]._zod;
          if (d.values) {
            c[u] ?? (c[u] = new Set());
            for (const f of d.values) c[u].add(f);
          }
        }
        return c;
      });
      const s = Ti,
        a = t.catchall;
      let o;
      e._zod.parse = (l, c) => {
        o ?? (o = n.value);
        const u = l.value;
        if (!s(u))
          return (
            l.issues.push({
              expected: "object",
              code: "invalid_type",
              input: u,
              inst: e,
            }),
            l
          );
        l.value = {};
        const d = [],
          f = o.shape;
        for (const m of o.keys) {
          const x = f[m]._zod.run({ value: u[m], issues: [] }, c);
          x instanceof Promise
            ? d.push(x.then((b) => zi(b, l, m, u)))
            : zi(x, l, m, u);
        }
        return a
          ? vg(d, u, l, c, n.value, e)
          : d.length
            ? Promise.all(d).then(() => l)
            : l;
      };
    }),
    z1 = C("$ZodObjectJIT", (e, t) => {
      D1.init(e, t);
      const r = e._zod.parse,
        n = $u(() => yg(t)),
        s = (m) => {
          const v = new n1(["shape", "payload", "ctx"]),
            x = n.value,
            b = (y) => {
              const w = tg(y);
              return `shape[${w}]._zod.run({ value: input[${w}], issues: [] }, ctx)`;
            };
          v.write("const input = payload.value;");
          const j = Object.create(null);
          let g = 0;
          for (const y of x.keys) j[y] = `key_${g++}`;
          v.write("const newResult = {};");
          for (const y of x.keys) {
            const w = j[y],
              S = tg(y);
            (v.write(`const ${w} = ${b(y)};`),
              v.write(`
        if (${w}.issues.length) {
          payload.issues = payload.issues.concat(${w}.issues.map(iss => ({
            ...iss,
            path: iss.path ? [${S}, ...iss.path] : [${S}]
          })));
        }
        
        
        if (${w}.value === undefined) {
          if (${S} in input) {
            newResult[${S}] = undefined;
          }
        } else {
          newResult[${S}] = ${w}.value;
        }
        
      `));
          }
          (v.write("payload.value = newResult;"), v.write("return payload;"));
          const p = v.compile();
          return (y, w) => p(m, y, w);
        };
      let a;
      const o = Ti,
        l = !Jh.jitless,
        u = l && Gb.value,
        d = t.catchall;
      let f;
      e._zod.parse = (m, v) => {
        f ?? (f = n.value);
        const x = m.value;
        return o(x)
          ? l && u && v?.async === !1 && v.jitless !== !0
            ? (a || (a = s(t.shape)),
              (m = a(m, v)),
              d ? vg([], x, m, v, f, e) : m)
            : r(m, v)
          : (m.issues.push({
              expected: "object",
              code: "invalid_type",
              input: x,
              inst: e,
            }),
            m);
      };
    });
  function xg(e, t, r, n) {
    for (const a of e)
      if (a.issues.length === 0) return ((t.value = a.value), t);
    const s = e.filter((a) => !Rs(a));
    return s.length === 1
      ? ((t.value = s[0].value), s[0])
      : (t.issues.push({
          code: "invalid_union",
          input: t.value,
          inst: r,
          errors: e.map((a) => a.issues.map((o) => Gr(o, n, Hr()))),
        }),
        t);
  }
  const M1 = C("$ZodUnion", (e, t) => {
      (Se.init(e, t),
        fe(e._zod, "optin", () =>
          t.options.some((s) => s._zod.optin === "optional")
            ? "optional"
            : void 0
        ),
        fe(e._zod, "optout", () =>
          t.options.some((s) => s._zod.optout === "optional")
            ? "optional"
            : void 0
        ),
        fe(e._zod, "values", () => {
          if (t.options.every((s) => s._zod.values))
            return new Set(t.options.flatMap((s) => Array.from(s._zod.values)));
        }),
        fe(e._zod, "pattern", () => {
          if (t.options.every((s) => s._zod.pattern)) {
            const s = t.options.map((a) => a._zod.pattern);
            return new RegExp(`^(${s.map((a) => Uu(a.source)).join("|")})$`);
          }
        }));
      const r = t.options.length === 1,
        n = t.options[0]._zod.run;
      e._zod.parse = (s, a) => {
        if (r) return n(s, a);
        let o = !1;
        const l = [];
        for (const c of t.options) {
          const u = c._zod.run({ value: s.value, issues: [] }, a);
          if (u instanceof Promise) (l.push(u), (o = !0));
          else {
            if (u.issues.length === 0) return u;
            l.push(u);
          }
        }
        return o ? Promise.all(l).then((c) => xg(c, s, e, a)) : xg(l, s, e, a);
      };
    }),
    $1 = C("$ZodIntersection", (e, t) => {
      (Se.init(e, t),
        (e._zod.parse = (r, n) => {
          const s = r.value,
            a = t.left._zod.run({ value: s, issues: [] }, n),
            o = t.right._zod.run({ value: s, issues: [] }, n);
          return a instanceof Promise || o instanceof Promise
            ? Promise.all([a, o]).then(([c, u]) => bg(r, c, u))
            : bg(r, a, o);
        }));
    });
  function Ku(e, t) {
    if (e === t) return { valid: !0, data: e };
    if (e instanceof Date && t instanceof Date && +e == +t)
      return { valid: !0, data: e };
    if (Ns(e) && Ns(t)) {
      const r = Object.keys(t),
        n = Object.keys(e).filter((a) => r.indexOf(a) !== -1),
        s = { ...e, ...t };
      for (const a of n) {
        const o = Ku(e[a], t[a]);
        if (!o.valid)
          return { valid: !1, mergeErrorPath: [a, ...o.mergeErrorPath] };
        s[a] = o.data;
      }
      return { valid: !0, data: s };
    }
    if (Array.isArray(e) && Array.isArray(t)) {
      if (e.length !== t.length) return { valid: !1, mergeErrorPath: [] };
      const r = [];
      for (let n = 0; n < e.length; n++) {
        const s = e[n],
          a = t[n],
          o = Ku(s, a);
        if (!o.valid)
          return { valid: !1, mergeErrorPath: [n, ...o.mergeErrorPath] };
        r.push(o.data);
      }
      return { valid: !0, data: r };
    }
    return { valid: !1, mergeErrorPath: [] };
  }
  function bg(e, t, r) {
    if (
      (t.issues.length && e.issues.push(...t.issues),
      r.issues.length && e.issues.push(...r.issues),
      Rs(e))
    )
      return e;
    const n = Ku(t.value, r.value);
    if (!n.valid)
      throw new Error(
        `Unmergable intersection. Error path: ${JSON.stringify(n.mergeErrorPath)}`
      );
    return ((e.value = n.data), e);
  }
  const F1 = C("$ZodRecord", (e, t) => {
      (Se.init(e, t),
        (e._zod.parse = (r, n) => {
          const s = r.value;
          if (!Ns(s))
            return (
              r.issues.push({
                expected: "record",
                code: "invalid_type",
                input: s,
                inst: e,
              }),
              r
            );
          const a = [],
            o = t.keyType._zod.values;
          if (o) {
            r.value = {};
            const l = new Set();
            for (const u of o)
              if (
                typeof u == "string" ||
                typeof u == "number" ||
                typeof u == "symbol"
              ) {
                l.add(typeof u == "number" ? u.toString() : u);
                const d = t.valueType._zod.run({ value: s[u], issues: [] }, n);
                d instanceof Promise
                  ? a.push(
                      d.then((f) => {
                        (f.issues.length && r.issues.push(...As(u, f.issues)),
                          (r.value[u] = f.value));
                      })
                    )
                  : (d.issues.length && r.issues.push(...As(u, d.issues)),
                    (r.value[u] = d.value));
              }
            let c;
            for (const u in s) l.has(u) || ((c = c ?? []), c.push(u));
            c &&
              c.length > 0 &&
              r.issues.push({
                code: "unrecognized_keys",
                input: s,
                inst: e,
                keys: c,
              });
          } else {
            r.value = {};
            for (const l of Reflect.ownKeys(s)) {
              if (l === "__proto__") continue;
              const c = t.keyType._zod.run({ value: l, issues: [] }, n);
              if (c instanceof Promise)
                throw new Error(
                  "Async schemas not supported in object keys currently"
                );
              if (c.issues.length) {
                t.mode === "loose"
                  ? (r.value[l] = s[l])
                  : r.issues.push({
                      code: "invalid_key",
                      origin: "record",
                      issues: c.issues.map((d) => Gr(d, n, Hr())),
                      input: l,
                      path: [l],
                      inst: e,
                    });
                continue;
              }
              const u = t.valueType._zod.run({ value: s[l], issues: [] }, n);
              u instanceof Promise
                ? a.push(
                    u.then((d) => {
                      (d.issues.length && r.issues.push(...As(l, d.issues)),
                        (r.value[c.value] = d.value));
                    })
                  )
                : (u.issues.length && r.issues.push(...As(l, u.issues)),
                  (r.value[c.value] = u.value));
            }
          }
          return a.length ? Promise.all(a).then(() => r) : r;
        }));
    }),
    U1 = C("$ZodEnum", (e, t) => {
      Se.init(e, t);
      const r = Xh(t.entries),
        n = new Set(r);
      ((e._zod.values = n),
        (e._zod.pattern = new RegExp(
          `^(${r
            .filter((s) => Yb.has(typeof s))
            .map((s) => (typeof s == "string" ? Pi(s) : s.toString()))
            .join("|")})$`
        )),
        (e._zod.parse = (s, a) => {
          const o = s.value;
          return (
            n.has(o) ||
              s.issues.push({
                code: "invalid_value",
                values: r,
                input: o,
                inst: e,
              }),
            s
          );
        }));
    }),
    Z1 = C("$ZodTransform", (e, t) => {
      (Se.init(e, t),
        (e._zod.parse = (r, n) => {
          if (n.direction === "backward") throw new Yh(e.constructor.name);
          const s = t.transform(r.value, r);
          if (n.async)
            return (s instanceof Promise ? s : Promise.resolve(s)).then(
              (o) => ((r.value = o), r)
            );
          if (s instanceof Promise) throw new js();
          return ((r.value = s), r);
        }));
    });
  function wg(e, t) {
    return e.issues.length && t === void 0 ? { issues: [], value: void 0 } : e;
  }
  const B1 = C("$ZodOptional", (e, t) => {
      (Se.init(e, t),
        (e._zod.optin = "optional"),
        (e._zod.optout = "optional"),
        fe(e._zod, "values", () =>
          t.innerType._zod.values
            ? new Set([...t.innerType._zod.values, void 0])
            : void 0
        ),
        fe(e._zod, "pattern", () => {
          const r = t.innerType._zod.pattern;
          return r ? new RegExp(`^(${Uu(r.source)})?$`) : void 0;
        }),
        (e._zod.parse = (r, n) => {
          if (t.innerType._zod.optin === "optional") {
            const s = t.innerType._zod.run(r, n);
            return s instanceof Promise
              ? s.then((a) => wg(a, r.value))
              : wg(s, r.value);
          }
          return r.value === void 0 ? r : t.innerType._zod.run(r, n);
        }));
    }),
    q1 = C("$ZodNullable", (e, t) => {
      (Se.init(e, t),
        fe(e._zod, "optin", () => t.innerType._zod.optin),
        fe(e._zod, "optout", () => t.innerType._zod.optout),
        fe(e._zod, "pattern", () => {
          const r = t.innerType._zod.pattern;
          return r ? new RegExp(`^(${Uu(r.source)}|null)$`) : void 0;
        }),
        fe(e._zod, "values", () =>
          t.innerType._zod.values
            ? new Set([...t.innerType._zod.values, null])
            : void 0
        ),
        (e._zod.parse = (r, n) =>
          r.value === null ? r : t.innerType._zod.run(r, n)));
    }),
    V1 = C("$ZodDefault", (e, t) => {
      (Se.init(e, t),
        (e._zod.optin = "optional"),
        fe(e._zod, "values", () => t.innerType._zod.values),
        (e._zod.parse = (r, n) => {
          if (n.direction === "backward") return t.innerType._zod.run(r, n);
          if (r.value === void 0) return ((r.value = t.defaultValue), r);
          const s = t.innerType._zod.run(r, n);
          return s instanceof Promise ? s.then((a) => kg(a, t)) : kg(s, t);
        }));
    });
  function kg(e, t) {
    return (e.value === void 0 && (e.value = t.defaultValue), e);
  }
  const K1 = C("$ZodPrefault", (e, t) => {
      (Se.init(e, t),
        (e._zod.optin = "optional"),
        fe(e._zod, "values", () => t.innerType._zod.values),
        (e._zod.parse = (r, n) => (
          n.direction === "backward" ||
            (r.value === void 0 && (r.value = t.defaultValue)),
          t.innerType._zod.run(r, n)
        )));
    }),
    W1 = C("$ZodNonOptional", (e, t) => {
      (Se.init(e, t),
        fe(e._zod, "values", () => {
          const r = t.innerType._zod.values;
          return r ? new Set([...r].filter((n) => n !== void 0)) : void 0;
        }),
        (e._zod.parse = (r, n) => {
          const s = t.innerType._zod.run(r, n);
          return s instanceof Promise ? s.then((a) => _g(a, e)) : _g(s, e);
        }));
    });
  function _g(e, t) {
    return (
      !e.issues.length &&
        e.value === void 0 &&
        e.issues.push({
          code: "invalid_type",
          expected: "nonoptional",
          input: e.value,
          inst: t,
        }),
      e
    );
  }
  const H1 = C("$ZodCatch", (e, t) => {
      (Se.init(e, t),
        fe(e._zod, "optin", () => t.innerType._zod.optin),
        fe(e._zod, "optout", () => t.innerType._zod.optout),
        fe(e._zod, "values", () => t.innerType._zod.values),
        (e._zod.parse = (r, n) => {
          if (n.direction === "backward") return t.innerType._zod.run(r, n);
          const s = t.innerType._zod.run(r, n);
          return s instanceof Promise
            ? s.then(
                (a) => (
                  (r.value = a.value),
                  a.issues.length &&
                    ((r.value = t.catchValue({
                      ...r,
                      error: { issues: a.issues.map((o) => Gr(o, n, Hr())) },
                      input: r.value,
                    })),
                    (r.issues = [])),
                  r
                )
              )
            : ((r.value = s.value),
              s.issues.length &&
                ((r.value = t.catchValue({
                  ...r,
                  error: { issues: s.issues.map((a) => Gr(a, n, Hr())) },
                  input: r.value,
                })),
                (r.issues = [])),
              r);
        }));
    }),
    Q1 = C("$ZodPipe", (e, t) => {
      (Se.init(e, t),
        fe(e._zod, "values", () => t.in._zod.values),
        fe(e._zod, "optin", () => t.in._zod.optin),
        fe(e._zod, "optout", () => t.out._zod.optout),
        fe(e._zod, "propValues", () => t.in._zod.propValues),
        (e._zod.parse = (r, n) => {
          if (n.direction === "backward") {
            const a = t.out._zod.run(r, n);
            return a instanceof Promise
              ? a.then((o) => Mi(o, t.in, n))
              : Mi(a, t.in, n);
          }
          const s = t.in._zod.run(r, n);
          return s instanceof Promise
            ? s.then((a) => Mi(a, t.out, n))
            : Mi(s, t.out, n);
        }));
    });
  function Mi(e, t, r) {
    return e.issues.length
      ? ((e.aborted = !0), e)
      : t._zod.run({ value: e.value, issues: e.issues }, r);
  }
  const G1 = C("$ZodReadonly", (e, t) => {
    (Se.init(e, t),
      fe(e._zod, "propValues", () => t.innerType._zod.propValues),
      fe(e._zod, "values", () => t.innerType._zod.values),
      fe(e._zod, "optin", () => t.innerType?._zod?.optin),
      fe(e._zod, "optout", () => t.innerType?._zod?.optout),
      (e._zod.parse = (r, n) => {
        if (n.direction === "backward") return t.innerType._zod.run(r, n);
        const s = t.innerType._zod.run(r, n);
        return s instanceof Promise ? s.then(Sg) : Sg(s);
      }));
  });
  function Sg(e) {
    return ((e.value = Object.freeze(e.value)), e);
  }
  const Y1 = C("$ZodCustom", (e, t) => {
    (mt.init(e, t),
      Se.init(e, t),
      (e._zod.parse = (r, n) => r),
      (e._zod.check = (r) => {
        const n = r.value,
          s = t.fn(n);
        if (s instanceof Promise) return s.then((a) => Cg(a, r, n, e));
        Cg(s, r, n, e);
      }));
  });
  function Cg(e, t, r, n) {
    if (!e) {
      const s = {
        code: "custom",
        input: r,
        inst: n,
        path: [...(n._zod.def.path ?? [])],
        continue: !n._zod.def.abort,
      };
      (n._zod.def.params && (s.params = n._zod.def.params),
        t.issues.push(Ua(s)));
    }
  }
  var jg;
  class J1 {
    constructor() {
      ((this._map = new WeakMap()), (this._idmap = new Map()));
    }
    add(t, ...r) {
      const n = r[0];
      if ((this._map.set(t, n), n && typeof n == "object" && "id" in n)) {
        if (this._idmap.has(n.id))
          throw new Error(`ID ${n.id} already exists in the registry`);
        this._idmap.set(n.id, t);
      }
      return this;
    }
    clear() {
      return ((this._map = new WeakMap()), (this._idmap = new Map()), this);
    }
    remove(t) {
      const r = this._map.get(t);
      return (
        r && typeof r == "object" && "id" in r && this._idmap.delete(r.id),
        this._map.delete(t),
        this
      );
    }
    get(t) {
      const r = t._zod.parent;
      if (r) {
        const n = { ...(this.get(r) ?? {}) };
        delete n.id;
        const s = { ...n, ...this._map.get(t) };
        return Object.keys(s).length ? s : void 0;
      }
      return this._map.get(t);
    }
    has(t) {
      return this._map.has(t);
    }
  }
  function X1() {
    return new J1();
  }
  (jg = globalThis).__zod_globalRegistry ?? (jg.__zod_globalRegistry = X1());
  const Za = globalThis.__zod_globalRegistry;
  function ek(e, t) {
    return new e({ type: "string", ...B(t) });
  }
  function tk(e, t) {
    return new e({
      type: "string",
      format: "email",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function Ng(e, t) {
    return new e({
      type: "string",
      format: "guid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function rk(e, t) {
    return new e({
      type: "string",
      format: "uuid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function nk(e, t) {
    return new e({
      type: "string",
      format: "uuid",
      check: "string_format",
      abort: !1,
      version: "v4",
      ...B(t),
    });
  }
  function sk(e, t) {
    return new e({
      type: "string",
      format: "uuid",
      check: "string_format",
      abort: !1,
      version: "v6",
      ...B(t),
    });
  }
  function ak(e, t) {
    return new e({
      type: "string",
      format: "uuid",
      check: "string_format",
      abort: !1,
      version: "v7",
      ...B(t),
    });
  }
  function ok(e, t) {
    return new e({
      type: "string",
      format: "url",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function ik(e, t) {
    return new e({
      type: "string",
      format: "emoji",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function lk(e, t) {
    return new e({
      type: "string",
      format: "nanoid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function ck(e, t) {
    return new e({
      type: "string",
      format: "cuid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function uk(e, t) {
    return new e({
      type: "string",
      format: "cuid2",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function dk(e, t) {
    return new e({
      type: "string",
      format: "ulid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function pk(e, t) {
    return new e({
      type: "string",
      format: "xid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function mk(e, t) {
    return new e({
      type: "string",
      format: "ksuid",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function fk(e, t) {
    return new e({
      type: "string",
      format: "ipv4",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function hk(e, t) {
    return new e({
      type: "string",
      format: "ipv6",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function gk(e, t) {
    return new e({
      type: "string",
      format: "cidrv4",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function yk(e, t) {
    return new e({
      type: "string",
      format: "cidrv6",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function vk(e, t) {
    return new e({
      type: "string",
      format: "base64",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function xk(e, t) {
    return new e({
      type: "string",
      format: "base64url",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function bk(e, t) {
    return new e({
      type: "string",
      format: "e164",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function wk(e, t) {
    return new e({
      type: "string",
      format: "jwt",
      check: "string_format",
      abort: !1,
      ...B(t),
    });
  }
  function kk(e, t) {
    return new e({
      type: "string",
      format: "datetime",
      check: "string_format",
      offset: !1,
      local: !1,
      precision: null,
      ...B(t),
    });
  }
  function _k(e, t) {
    return new e({
      type: "string",
      format: "date",
      check: "string_format",
      ...B(t),
    });
  }
  function Sk(e, t) {
    return new e({
      type: "string",
      format: "time",
      check: "string_format",
      precision: null,
      ...B(t),
    });
  }
  function Ck(e, t) {
    return new e({
      type: "string",
      format: "duration",
      check: "string_format",
      ...B(t),
    });
  }
  function jk(e, t) {
    return new e({ type: "number", checks: [], ...B(t) });
  }
  function Nk(e, t) {
    return new e({
      type: "number",
      check: "number_format",
      abort: !1,
      format: "safeint",
      ...B(t),
    });
  }
  function Rk(e, t) {
    return new e({ type: "boolean", ...B(t) });
  }
  function Ak(e) {
    return new e({ type: "any" });
  }
  function Ek(e) {
    return new e({ type: "unknown" });
  }
  function Tk(e, t) {
    return new e({ type: "never", ...B(t) });
  }
  function Rg(e, t) {
    return new pg({ check: "less_than", ...B(t), value: e, inclusive: !1 });
  }
  function Wu(e, t) {
    return new pg({ check: "less_than", ...B(t), value: e, inclusive: !0 });
  }
  function Ag(e, t) {
    return new mg({ check: "greater_than", ...B(t), value: e, inclusive: !1 });
  }
  function Hu(e, t) {
    return new mg({ check: "greater_than", ...B(t), value: e, inclusive: !0 });
  }
  function Eg(e, t) {
    return new Vw({ check: "multiple_of", ...B(t), value: e });
  }
  function Tg(e, t) {
    return new Ww({ check: "max_length", ...B(t), maximum: e });
  }
  function $i(e, t) {
    return new Hw({ check: "min_length", ...B(t), minimum: e });
  }
  function Pg(e, t) {
    return new Qw({ check: "length_equals", ...B(t), length: e });
  }
  function Pk(e, t) {
    return new Gw({
      check: "string_format",
      format: "regex",
      ...B(t),
      pattern: e,
    });
  }
  function Ok(e) {
    return new Yw({ check: "string_format", format: "lowercase", ...B(e) });
  }
  function Ik(e) {
    return new Jw({ check: "string_format", format: "uppercase", ...B(e) });
  }
  function Lk(e, t) {
    return new Xw({
      check: "string_format",
      format: "includes",
      ...B(t),
      includes: e,
    });
  }
  function Dk(e, t) {
    return new e1({
      check: "string_format",
      format: "starts_with",
      ...B(t),
      prefix: e,
    });
  }
  function zk(e, t) {
    return new t1({
      check: "string_format",
      format: "ends_with",
      ...B(t),
      suffix: e,
    });
  }
  function Es(e) {
    return new r1({ check: "overwrite", tx: e });
  }
  function Mk(e) {
    return Es((t) => t.normalize(e));
  }
  function $k() {
    return Es((e) => e.trim());
  }
  function Fk() {
    return Es((e) => e.toLowerCase());
  }
  function Uk() {
    return Es((e) => e.toUpperCase());
  }
  function Zk() {
    return Es((e) => Qb(e));
  }
  function Bk(e, t, r) {
    return new e({ type: "array", element: t, ...B(r) });
  }
  function qk(e, t, r) {
    return new e({ type: "custom", check: "custom", fn: t, ...B(r) });
  }
  function Vk(e) {
    const t = Kk(
      (r) => (
        (r.addIssue = (n) => {
          if (typeof n == "string") r.issues.push(Ua(n, r.value, t._zod.def));
          else {
            const s = n;
            (s.fatal && (s.continue = !1),
              s.code ?? (s.code = "custom"),
              s.input ?? (s.input = r.value),
              s.inst ?? (s.inst = t),
              s.continue ?? (s.continue = !t._zod.def.abort),
              r.issues.push(Ua(s)));
          }
        }),
        e(r.value, r)
      )
    );
    return t;
  }
  function Kk(e, t) {
    const r = new mt({ check: "custom", ...B(t) });
    return ((r._zod.check = e), r);
  }
  function Og(e) {
    let t = e?.target ?? "draft-2020-12";
    return (
      t === "draft-4" && (t = "draft-04"),
      t === "draft-7" && (t = "draft-07"),
      {
        processors: e.processors ?? {},
        metadataRegistry: e?.metadata ?? Za,
        target: t,
        unrepresentable: e?.unrepresentable ?? "throw",
        override: e?.override ?? (() => {}),
        io: e?.io ?? "output",
        counter: 0,
        seen: new Map(),
        cycles: e?.cycles ?? "ref",
        reused: e?.reused ?? "inline",
        external: e?.external ?? void 0,
      }
    );
  }
  function Fe(e, t, r = { path: [], schemaPath: [] }) {
    var n;
    const s = e._zod.def,
      a = t.seen.get(e);
    if (a)
      return (
        a.count++,
        r.schemaPath.includes(e) && (a.cycle = r.path),
        a.schema
      );
    const o = { schema: {}, count: 1, cycle: void 0, path: r.path };
    t.seen.set(e, o);
    const l = e._zod.toJSONSchema?.();
    if (l) o.schema = l;
    else {
      const d = { ...r, schemaPath: [...r.schemaPath, e], path: r.path },
        f = e._zod.parent;
      if (f) ((o.ref = f), Fe(f, t, d), (t.seen.get(f).isParent = !0));
      else if (e._zod.processJSONSchema)
        e._zod.processJSONSchema(t, o.schema, d);
      else {
        const m = o.schema,
          v = t.processors[s.type];
        if (!v)
          throw new Error(
            `[toJSONSchema]: Non-representable type encountered: ${s.type}`
          );
        v(e, t, m, d);
      }
    }
    const c = t.metadataRegistry.get(e);
    return (
      c && Object.assign(o.schema, c),
      t.io === "input" &&
        ot(e) &&
        (delete o.schema.examples, delete o.schema.default),
      t.io === "input" &&
        o.schema._prefault &&
        ((n = o.schema).default ?? (n.default = o.schema._prefault)),
      delete o.schema._prefault,
      t.seen.get(e).schema
    );
  }
  function Ig(e, t) {
    const r = e.seen.get(t);
    if (!r) throw new Error("Unprocessed schema. This is a bug in Zod.");
    const n = (a) => {
        const o = e.target === "draft-2020-12" ? "$defs" : "definitions";
        if (e.external) {
          const d = e.external.registry.get(a[0])?.id,
            f = e.external.uri ?? ((v) => v);
          if (d) return { ref: f(d) };
          const m = a[1].defId ?? a[1].schema.id ?? `schema${e.counter++}`;
          return (
            (a[1].defId = m),
            { defId: m, ref: `${f("__shared")}#/${o}/${m}` }
          );
        }
        if (a[1] === r) return { ref: "#" };
        const c = `#/${o}/`,
          u = a[1].schema.id ?? `__schema${e.counter++}`;
        return { defId: u, ref: c + u };
      },
      s = (a) => {
        if (a[1].schema.$ref) return;
        const o = a[1],
          { ref: l, defId: c } = n(a);
        ((o.def = { ...o.schema }), c && (o.defId = c));
        const u = o.schema;
        for (const d in u) delete u[d];
        u.$ref = l;
      };
    if (e.cycles === "throw")
      for (const a of e.seen.entries()) {
        const o = a[1];
        if (o.cycle)
          throw new Error(`Cycle detected: #/${o.cycle?.join("/")}/<root>

Set the \`cycles\` parameter to \`"ref"\` to resolve cyclical schemas with defs.`);
      }
    for (const a of e.seen.entries()) {
      const o = a[1];
      if (t === a[0]) {
        s(a);
        continue;
      }
      if (e.external) {
        const c = e.external.registry.get(a[0])?.id;
        if (t !== a[0] && c) {
          s(a);
          continue;
        }
      }
      if (e.metadataRegistry.get(a[0])?.id) {
        s(a);
        continue;
      }
      if (o.cycle) {
        s(a);
        continue;
      }
      if (o.count > 1 && e.reused === "ref") {
        s(a);
        continue;
      }
    }
  }
  function Lg(e, t) {
    const r = e.seen.get(t);
    if (!r) throw new Error("Unprocessed schema. This is a bug in Zod.");
    const n = (o) => {
      const l = e.seen.get(o),
        c = l.def ?? l.schema,
        u = { ...c };
      if (l.ref === null) return;
      const d = l.ref;
      if (((l.ref = null), d)) {
        n(d);
        const f = e.seen.get(d).schema;
        f.$ref &&
        (e.target === "draft-07" ||
          e.target === "draft-04" ||
          e.target === "openapi-3.0")
          ? ((c.allOf = c.allOf ?? []), c.allOf.push(f))
          : (Object.assign(c, f), Object.assign(c, u));
      }
      l.isParent ||
        e.override({ zodSchema: o, jsonSchema: c, path: l.path ?? [] });
    };
    for (const o of [...e.seen.entries()].reverse()) n(o[0]);
    const s = {};
    if (
      (e.target === "draft-2020-12"
        ? (s.$schema = "https://json-schema.org/draft/2020-12/schema")
        : e.target === "draft-07"
          ? (s.$schema = "http://json-schema.org/draft-07/schema#")
          : e.target === "draft-04"
            ? (s.$schema = "http://json-schema.org/draft-04/schema#")
            : e.target,
      e.external?.uri)
    ) {
      const o = e.external.registry.get(t)?.id;
      if (!o) throw new Error("Schema is missing an `id` property");
      s.$id = e.external.uri(o);
    }
    Object.assign(s, r.def ?? r.schema);
    const a = e.external?.defs ?? {};
    for (const o of e.seen.entries()) {
      const l = o[1];
      l.def && l.defId && (a[l.defId] = l.def);
    }
    e.external ||
      (Object.keys(a).length > 0 &&
        (e.target === "draft-2020-12" ? (s.$defs = a) : (s.definitions = a)));
    try {
      const o = JSON.parse(JSON.stringify(s));
      return (
        Object.defineProperty(o, "~standard", {
          value: {
            ...t["~standard"],
            jsonSchema: { input: Fi(t, "input"), output: Fi(t, "output") },
          },
          enumerable: !1,
          writable: !1,
        }),
        o
      );
    } catch {
      throw new Error("Error converting schema to JSON.");
    }
  }
  function ot(e, t) {
    const r = t ?? { seen: new Set() };
    if (r.seen.has(e)) return !1;
    r.seen.add(e);
    const n = e._zod.def;
    if (n.type === "transform") return !0;
    if (n.type === "array") return ot(n.element, r);
    if (n.type === "set") return ot(n.valueType, r);
    if (n.type === "lazy") return ot(n.getter(), r);
    if (
      n.type === "promise" ||
      n.type === "optional" ||
      n.type === "nonoptional" ||
      n.type === "nullable" ||
      n.type === "readonly" ||
      n.type === "default" ||
      n.type === "prefault"
    )
      return ot(n.innerType, r);
    if (n.type === "intersection") return ot(n.left, r) || ot(n.right, r);
    if (n.type === "record" || n.type === "map")
      return ot(n.keyType, r) || ot(n.valueType, r);
    if (n.type === "pipe") return ot(n.in, r) || ot(n.out, r);
    if (n.type === "object") {
      for (const s in n.shape) if (ot(n.shape[s], r)) return !0;
      return !1;
    }
    if (n.type === "union") {
      for (const s of n.options) if (ot(s, r)) return !0;
      return !1;
    }
    if (n.type === "tuple") {
      for (const s of n.items) if (ot(s, r)) return !0;
      return !!(n.rest && ot(n.rest, r));
    }
    return !1;
  }
  const Wk =
      (e, t = {}) =>
      (r) => {
        const n = Og({ ...r, processors: t });
        return (Fe(e, n), Ig(n, e), Lg(n, e));
      },
    Fi = (e, t) => (r) => {
      const { libraryOptions: n, target: s } = r ?? {},
        a = Og({ ...(n ?? {}), target: s, io: t, processors: {} });
      return (Fe(e, a), Ig(a, e), Lg(a, e));
    },
    Hk = {
      guid: "uuid",
      url: "uri",
      datetime: "date-time",
      json_string: "json-string",
      regex: "",
    },
    Qk = (e, t, r, n) => {
      const s = r;
      s.type = "string";
      const {
        minimum: a,
        maximum: o,
        format: l,
        patterns: c,
        contentEncoding: u,
      } = e._zod.bag;
      if (
        (typeof a == "number" && (s.minLength = a),
        typeof o == "number" && (s.maxLength = o),
        l && ((s.format = Hk[l] ?? l), s.format === "" && delete s.format),
        u && (s.contentEncoding = u),
        c && c.size > 0)
      ) {
        const d = [...c];
        d.length === 1
          ? (s.pattern = d[0].source)
          : d.length > 1 &&
            (s.allOf = [
              ...d.map((f) => ({
                ...(t.target === "draft-07" ||
                t.target === "draft-04" ||
                t.target === "openapi-3.0"
                  ? { type: "string" }
                  : {}),
                pattern: f.source,
              })),
            ]);
      }
    },
    Gk = (e, t, r, n) => {
      const s = r,
        {
          minimum: a,
          maximum: o,
          format: l,
          multipleOf: c,
          exclusiveMaximum: u,
          exclusiveMinimum: d,
        } = e._zod.bag;
      (typeof l == "string" && l.includes("int")
        ? (s.type = "integer")
        : (s.type = "number"),
        typeof d == "number" &&
          (t.target === "draft-04" || t.target === "openapi-3.0"
            ? ((s.minimum = d), (s.exclusiveMinimum = !0))
            : (s.exclusiveMinimum = d)),
        typeof a == "number" &&
          ((s.minimum = a),
          typeof d == "number" &&
            t.target !== "draft-04" &&
            (d >= a ? delete s.minimum : delete s.exclusiveMinimum)),
        typeof u == "number" &&
          (t.target === "draft-04" || t.target === "openapi-3.0"
            ? ((s.maximum = u), (s.exclusiveMaximum = !0))
            : (s.exclusiveMaximum = u)),
        typeof o == "number" &&
          ((s.maximum = o),
          typeof u == "number" &&
            t.target !== "draft-04" &&
            (u <= o ? delete s.maximum : delete s.exclusiveMaximum)),
        typeof c == "number" && (s.multipleOf = c));
    },
    Yk = (e, t, r, n) => {
      r.type = "boolean";
    },
    Jk = (e, t, r, n) => {
      r.not = {};
    },
    Xk = (e, t, r, n) => {},
    e_ = (e, t, r, n) => {},
    t_ = (e, t, r, n) => {
      const s = e._zod.def,
        a = Xh(s.entries);
      (a.every((o) => typeof o == "number") && (r.type = "number"),
        a.every((o) => typeof o == "string") && (r.type = "string"),
        (r.enum = a));
    },
    r_ = (e, t, r, n) => {
      if (t.unrepresentable === "throw")
        throw new Error("Custom types cannot be represented in JSON Schema");
    },
    n_ = (e, t, r, n) => {
      if (t.unrepresentable === "throw")
        throw new Error("Transforms cannot be represented in JSON Schema");
    },
    s_ = (e, t, r, n) => {
      const s = r,
        a = e._zod.def,
        { minimum: o, maximum: l } = e._zod.bag;
      (typeof o == "number" && (s.minItems = o),
        typeof l == "number" && (s.maxItems = l),
        (s.type = "array"),
        (s.items = Fe(a.element, t, { ...n, path: [...n.path, "items"] })));
    },
    a_ = (e, t, r, n) => {
      const s = r,
        a = e._zod.def;
      ((s.type = "object"), (s.properties = {}));
      const o = a.shape;
      for (const u in o)
        s.properties[u] = Fe(o[u], t, {
          ...n,
          path: [...n.path, "properties", u],
        });
      const l = new Set(Object.keys(o)),
        c = new Set(
          [...l].filter((u) => {
            const d = a.shape[u]._zod;
            return t.io === "input" ? d.optin === void 0 : d.optout === void 0;
          })
        );
      (c.size > 0 && (s.required = Array.from(c)),
        a.catchall?._zod.def.type === "never"
          ? (s.additionalProperties = !1)
          : a.catchall
            ? a.catchall &&
              (s.additionalProperties = Fe(a.catchall, t, {
                ...n,
                path: [...n.path, "additionalProperties"],
              }))
            : t.io === "output" && (s.additionalProperties = !1));
    },
    o_ = (e, t, r, n) => {
      const s = e._zod.def,
        a = s.inclusive === !1,
        o = s.options.map((l, c) =>
          Fe(l, t, { ...n, path: [...n.path, a ? "oneOf" : "anyOf", c] })
        );
      a ? (r.oneOf = o) : (r.anyOf = o);
    },
    i_ = (e, t, r, n) => {
      const s = e._zod.def,
        a = Fe(s.left, t, { ...n, path: [...n.path, "allOf", 0] }),
        o = Fe(s.right, t, { ...n, path: [...n.path, "allOf", 1] }),
        l = (u) => "allOf" in u && Object.keys(u).length === 1,
        c = [...(l(a) ? a.allOf : [a]), ...(l(o) ? o.allOf : [o])];
      r.allOf = c;
    },
    l_ = (e, t, r, n) => {
      const s = r,
        a = e._zod.def;
      ((s.type = "object"),
        (t.target === "draft-07" || t.target === "draft-2020-12") &&
          (s.propertyNames = Fe(a.keyType, t, {
            ...n,
            path: [...n.path, "propertyNames"],
          })),
        (s.additionalProperties = Fe(a.valueType, t, {
          ...n,
          path: [...n.path, "additionalProperties"],
        })));
    },
    c_ = (e, t, r, n) => {
      const s = e._zod.def,
        a = Fe(s.innerType, t, n),
        o = t.seen.get(e);
      t.target === "openapi-3.0"
        ? ((o.ref = s.innerType), (r.nullable = !0))
        : (r.anyOf = [a, { type: "null" }]);
    },
    u_ = (e, t, r, n) => {
      const s = e._zod.def;
      Fe(s.innerType, t, n);
      const a = t.seen.get(e);
      a.ref = s.innerType;
    },
    d_ = (e, t, r, n) => {
      const s = e._zod.def;
      Fe(s.innerType, t, n);
      const a = t.seen.get(e);
      ((a.ref = s.innerType),
        (r.default = JSON.parse(JSON.stringify(s.defaultValue))));
    },
    p_ = (e, t, r, n) => {
      const s = e._zod.def;
      Fe(s.innerType, t, n);
      const a = t.seen.get(e);
      ((a.ref = s.innerType),
        t.io === "input" &&
          (r._prefault = JSON.parse(JSON.stringify(s.defaultValue))));
    },
    m_ = (e, t, r, n) => {
      const s = e._zod.def;
      Fe(s.innerType, t, n);
      const a = t.seen.get(e);
      a.ref = s.innerType;
      let o;
      try {
        o = s.catchValue(void 0);
      } catch {
        throw new Error(
          "Dynamic catch values are not supported in JSON Schema"
        );
      }
      r.default = o;
    },
    f_ = (e, t, r, n) => {
      const s = e._zod.def,
        a =
          t.io === "input"
            ? s.in._zod.def.type === "transform"
              ? s.out
              : s.in
            : s.out;
      Fe(a, t, n);
      const o = t.seen.get(e);
      o.ref = a;
    },
    h_ = (e, t, r, n) => {
      const s = e._zod.def;
      Fe(s.innerType, t, n);
      const a = t.seen.get(e);
      ((a.ref = s.innerType), (r.readOnly = !0));
    },
    g_ = (e, t, r, n) => {
      const s = e._zod.def;
      Fe(s.innerType, t, n);
      const a = t.seen.get(e);
      a.ref = s.innerType;
    },
    y_ = C("ZodISODateTime", (e, t) => {
      (g1.init(e, t), Ce.init(e, t));
    });
  function v_(e) {
    return kk(y_, e);
  }
  const x_ = C("ZodISODate", (e, t) => {
    (y1.init(e, t), Ce.init(e, t));
  });
  function b_(e) {
    return _k(x_, e);
  }
  const w_ = C("ZodISOTime", (e, t) => {
    (v1.init(e, t), Ce.init(e, t));
  });
  function k_(e) {
    return Sk(w_, e);
  }
  const __ = C("ZodISODuration", (e, t) => {
    (x1.init(e, t), Ce.init(e, t));
  });
  function S_(e) {
    return Ck(__, e);
  }
  const It = C(
      "ZodError",
      (e, t) => {
        (ag.init(e, t),
          (e.name = "ZodError"),
          Object.defineProperties(e, {
            format: { value: (r) => lw(e, r) },
            flatten: { value: (r) => iw(e, r) },
            addIssue: {
              value: (r) => {
                (e.issues.push(r),
                  (e.message = JSON.stringify(e.issues, Mu, 2)));
              },
            },
            addIssues: {
              value: (r) => {
                (e.issues.push(...r),
                  (e.message = JSON.stringify(e.issues, Mu, 2)));
              },
            },
            isEmpty: {
              get() {
                return e.issues.length === 0;
              },
            },
          }));
      },
      { Parent: Error }
    ),
    C_ = Bu(It),
    j_ = qu(It),
    N_ = Ii(It),
    R_ = Li(It),
    A_ = dw(It),
    E_ = pw(It),
    T_ = mw(It),
    P_ = fw(It),
    O_ = hw(It),
    I_ = gw(It),
    L_ = yw(It),
    D_ = vw(It),
    Ae = C(
      "ZodType",
      (e, t) => (
        Se.init(e, t),
        Object.assign(e["~standard"], {
          jsonSchema: { input: Fi(e, "input"), output: Fi(e, "output") },
        }),
        (e.toJSONSchema = Wk(e, {})),
        (e.def = t),
        (e.type = t.type),
        Object.defineProperty(e, "_def", { value: t }),
        (e.check = (...r) =>
          e.clone(
            En(t, {
              checks: [
                ...(t.checks ?? []),
                ...r.map((n) =>
                  typeof n == "function"
                    ? {
                        _zod: {
                          check: n,
                          def: { check: "custom" },
                          onattach: [],
                        },
                      }
                    : n
                ),
              ],
            })
          )),
        (e.clone = (r, n) => Qr(e, r, n)),
        (e.brand = () => e),
        (e.register = (r, n) => (r.add(e, n), e)),
        (e.parse = (r, n) => C_(e, r, n, { callee: e.parse })),
        (e.safeParse = (r, n) => N_(e, r, n)),
        (e.parseAsync = async (r, n) => j_(e, r, n, { callee: e.parseAsync })),
        (e.safeParseAsync = async (r, n) => R_(e, r, n)),
        (e.spa = e.safeParseAsync),
        (e.encode = (r, n) => A_(e, r, n)),
        (e.decode = (r, n) => E_(e, r, n)),
        (e.encodeAsync = async (r, n) => T_(e, r, n)),
        (e.decodeAsync = async (r, n) => P_(e, r, n)),
        (e.safeEncode = (r, n) => O_(e, r, n)),
        (e.safeDecode = (r, n) => I_(e, r, n)),
        (e.safeEncodeAsync = async (r, n) => L_(e, r, n)),
        (e.safeDecodeAsync = async (r, n) => D_(e, r, n)),
        (e.refine = (r, n) => e.check(RS(r, n))),
        (e.superRefine = (r) => e.check(AS(r))),
        (e.overwrite = (r) => e.check(Es(r))),
        (e.optional = () => Zg(e)),
        (e.nullable = () => Bg(e)),
        (e.nullish = () => Zg(Bg(e))),
        (e.nonoptional = (r) => wS(e, r)),
        (e.array = () => He(e)),
        (e.or = (r) => uS([e, r])),
        (e.and = (r) => pS(e, r)),
        (e.transform = (r) => Vg(e, hS(r))),
        (e.default = (r) => vS(e, r)),
        (e.prefault = (r) => bS(e, r)),
        (e.catch = (r) => _S(e, r)),
        (e.pipe = (r) => Vg(e, r)),
        (e.readonly = () => jS(e)),
        (e.describe = (r) => {
          const n = e.clone();
          return (Za.add(n, { description: r }), n);
        }),
        Object.defineProperty(e, "description", {
          get() {
            return Za.get(e)?.description;
          },
          configurable: !0,
        }),
        (e.meta = (...r) => {
          if (r.length === 0) return Za.get(e);
          const n = e.clone();
          return (Za.add(n, r[0]), n);
        }),
        (e.isOptional = () => e.safeParse(void 0).success),
        (e.isNullable = () => e.safeParse(null).success),
        e
      )
    ),
    Dg = C("_ZodString", (e, t) => {
      (Vu.init(e, t),
        Ae.init(e, t),
        (e._zod.processJSONSchema = (n, s, a) => Qk(e, n, s)));
      const r = e._zod.bag;
      ((e.format = r.format ?? null),
        (e.minLength = r.minimum ?? null),
        (e.maxLength = r.maximum ?? null),
        (e.regex = (...n) => e.check(Pk(...n))),
        (e.includes = (...n) => e.check(Lk(...n))),
        (e.startsWith = (...n) => e.check(Dk(...n))),
        (e.endsWith = (...n) => e.check(zk(...n))),
        (e.min = (...n) => e.check($i(...n))),
        (e.max = (...n) => e.check(Tg(...n))),
        (e.length = (...n) => e.check(Pg(...n))),
        (e.nonempty = (...n) => e.check($i(1, ...n))),
        (e.lowercase = (n) => e.check(Ok(n))),
        (e.uppercase = (n) => e.check(Ik(n))),
        (e.trim = () => e.check($k())),
        (e.normalize = (...n) => e.check(Mk(...n))),
        (e.toLowerCase = () => e.check(Fk())),
        (e.toUpperCase = () => e.check(Uk())),
        (e.slugify = () => e.check(Zk())));
    }),
    z_ = C("ZodString", (e, t) => {
      (Vu.init(e, t),
        Dg.init(e, t),
        (e.email = (r) => e.check(tk(M_, r))),
        (e.url = (r) => e.check(ok($_, r))),
        (e.jwt = (r) => e.check(wk(eS, r))),
        (e.emoji = (r) => e.check(ik(F_, r))),
        (e.guid = (r) => e.check(Ng(zg, r))),
        (e.uuid = (r) => e.check(rk(Ui, r))),
        (e.uuidv4 = (r) => e.check(nk(Ui, r))),
        (e.uuidv6 = (r) => e.check(sk(Ui, r))),
        (e.uuidv7 = (r) => e.check(ak(Ui, r))),
        (e.nanoid = (r) => e.check(lk(U_, r))),
        (e.guid = (r) => e.check(Ng(zg, r))),
        (e.cuid = (r) => e.check(ck(Z_, r))),
        (e.cuid2 = (r) => e.check(uk(B_, r))),
        (e.ulid = (r) => e.check(dk(q_, r))),
        (e.base64 = (r) => e.check(vk(Y_, r))),
        (e.base64url = (r) => e.check(xk(J_, r))),
        (e.xid = (r) => e.check(pk(V_, r))),
        (e.ksuid = (r) => e.check(mk(K_, r))),
        (e.ipv4 = (r) => e.check(fk(W_, r))),
        (e.ipv6 = (r) => e.check(hk(H_, r))),
        (e.cidrv4 = (r) => e.check(gk(Q_, r))),
        (e.cidrv6 = (r) => e.check(yk(G_, r))),
        (e.e164 = (r) => e.check(bk(X_, r))),
        (e.datetime = (r) => e.check(v_(r))),
        (e.date = (r) => e.check(b_(r))),
        (e.time = (r) => e.check(k_(r))),
        (e.duration = (r) => e.check(S_(r))));
    });
  function oe(e) {
    return ek(z_, e);
  }
  const Ce = C("ZodStringFormat", (e, t) => {
      (xe.init(e, t), Dg.init(e, t));
    }),
    M_ = C("ZodEmail", (e, t) => {
      (i1.init(e, t), Ce.init(e, t));
    }),
    zg = C("ZodGUID", (e, t) => {
      (a1.init(e, t), Ce.init(e, t));
    }),
    Ui = C("ZodUUID", (e, t) => {
      (o1.init(e, t), Ce.init(e, t));
    }),
    $_ = C("ZodURL", (e, t) => {
      (l1.init(e, t), Ce.init(e, t));
    }),
    F_ = C("ZodEmoji", (e, t) => {
      (c1.init(e, t), Ce.init(e, t));
    }),
    U_ = C("ZodNanoID", (e, t) => {
      (u1.init(e, t), Ce.init(e, t));
    }),
    Z_ = C("ZodCUID", (e, t) => {
      (d1.init(e, t), Ce.init(e, t));
    }),
    B_ = C("ZodCUID2", (e, t) => {
      (p1.init(e, t), Ce.init(e, t));
    }),
    q_ = C("ZodULID", (e, t) => {
      (m1.init(e, t), Ce.init(e, t));
    }),
    V_ = C("ZodXID", (e, t) => {
      (f1.init(e, t), Ce.init(e, t));
    }),
    K_ = C("ZodKSUID", (e, t) => {
      (h1.init(e, t), Ce.init(e, t));
    }),
    W_ = C("ZodIPv4", (e, t) => {
      (b1.init(e, t), Ce.init(e, t));
    }),
    H_ = C("ZodIPv6", (e, t) => {
      (w1.init(e, t), Ce.init(e, t));
    }),
    Q_ = C("ZodCIDRv4", (e, t) => {
      (k1.init(e, t), Ce.init(e, t));
    }),
    G_ = C("ZodCIDRv6", (e, t) => {
      (_1.init(e, t), Ce.init(e, t));
    }),
    Y_ = C("ZodBase64", (e, t) => {
      (S1.init(e, t), Ce.init(e, t));
    }),
    J_ = C("ZodBase64URL", (e, t) => {
      (j1.init(e, t), Ce.init(e, t));
    }),
    X_ = C("ZodE164", (e, t) => {
      (N1.init(e, t), Ce.init(e, t));
    }),
    eS = C("ZodJWT", (e, t) => {
      (A1.init(e, t), Ce.init(e, t));
    }),
    Mg = C("ZodNumber", (e, t) => {
      (hg.init(e, t),
        Ae.init(e, t),
        (e._zod.processJSONSchema = (n, s, a) => Gk(e, n, s)),
        (e.gt = (n, s) => e.check(Ag(n, s))),
        (e.gte = (n, s) => e.check(Hu(n, s))),
        (e.min = (n, s) => e.check(Hu(n, s))),
        (e.lt = (n, s) => e.check(Rg(n, s))),
        (e.lte = (n, s) => e.check(Wu(n, s))),
        (e.max = (n, s) => e.check(Wu(n, s))),
        (e.int = (n) => e.check($g(n))),
        (e.safe = (n) => e.check($g(n))),
        (e.positive = (n) => e.check(Ag(0, n))),
        (e.nonnegative = (n) => e.check(Hu(0, n))),
        (e.negative = (n) => e.check(Rg(0, n))),
        (e.nonpositive = (n) => e.check(Wu(0, n))),
        (e.multipleOf = (n, s) => e.check(Eg(n, s))),
        (e.step = (n, s) => e.check(Eg(n, s))),
        (e.finite = () => e));
      const r = e._zod.bag;
      ((e.minValue =
        Math.max(
          r.minimum ?? Number.NEGATIVE_INFINITY,
          r.exclusiveMinimum ?? Number.NEGATIVE_INFINITY
        ) ?? null),
        (e.maxValue =
          Math.min(
            r.maximum ?? Number.POSITIVE_INFINITY,
            r.exclusiveMaximum ?? Number.POSITIVE_INFINITY
          ) ?? null),
        (e.isInt =
          (r.format ?? "").includes("int") ||
          Number.isSafeInteger(r.multipleOf ?? 0.5)),
        (e.isFinite = !0),
        (e.format = r.format ?? null));
    });
  function ue(e) {
    return jk(Mg, e);
  }
  const tS = C("ZodNumberFormat", (e, t) => {
    (E1.init(e, t), Mg.init(e, t));
  });
  function $g(e) {
    return Nk(tS, e);
  }
  const rS = C("ZodBoolean", (e, t) => {
    (T1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => Yk(e, r, n)));
  });
  function _t(e) {
    return Rk(rS, e);
  }
  const nS = C("ZodAny", (e, t) => {
    (P1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => Xk()));
  });
  function Fg() {
    return Ak(nS);
  }
  const sS = C("ZodUnknown", (e, t) => {
    (O1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => e_()));
  });
  function Ba() {
    return Ek(sS);
  }
  const aS = C("ZodNever", (e, t) => {
    (I1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => Jk(e, r, n)));
  });
  function oS(e) {
    return Tk(aS, e);
  }
  const iS = C("ZodArray", (e, t) => {
    (L1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => s_(e, r, n, s)),
      (e.element = t.element),
      (e.min = (r, n) => e.check($i(r, n))),
      (e.nonempty = (r) => e.check($i(1, r))),
      (e.max = (r, n) => e.check(Tg(r, n))),
      (e.length = (r, n) => e.check(Pg(r, n))),
      (e.unwrap = () => e.element));
  });
  function He(e, t) {
    return Bk(iS, e, t);
  }
  const lS = C("ZodObject", (e, t) => {
    (z1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => a_(e, r, n, s)),
      fe(e, "shape", () => t.shape),
      (e.keyof = () => Tn(Object.keys(e._zod.def.shape))),
      (e.catchall = (r) => e.clone({ ...e._zod.def, catchall: r })),
      (e.passthrough = () => e.clone({ ...e._zod.def, catchall: Ba() })),
      (e.loose = () => e.clone({ ...e._zod.def, catchall: Ba() })),
      (e.strict = () => e.clone({ ...e._zod.def, catchall: oS() })),
      (e.strip = () => e.clone({ ...e._zod.def, catchall: void 0 })),
      (e.extend = (r) => rw(e, r)),
      (e.safeExtend = (r) => nw(e, r)),
      (e.merge = (r) => sw(e, r)),
      (e.pick = (r) => ew(e, r)),
      (e.omit = (r) => tw(e, r)),
      (e.partial = (...r) => aw(Ug, e, r[0])),
      (e.required = (...r) => ow(qg, e, r[0])));
  });
  function W(e, t) {
    const r = { type: "object", shape: e ?? {}, ...B(t) };
    return new lS(r);
  }
  const cS = C("ZodUnion", (e, t) => {
    (M1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => o_(e, r, n, s)),
      (e.options = t.options));
  });
  function uS(e, t) {
    return new cS({ type: "union", options: e, ...B(t) });
  }
  const dS = C("ZodIntersection", (e, t) => {
    ($1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => i_(e, r, n, s)));
  });
  function pS(e, t) {
    return new dS({ type: "intersection", left: e, right: t });
  }
  const mS = C("ZodRecord", (e, t) => {
    (F1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => l_(e, r, n, s)),
      (e.keyType = t.keyType),
      (e.valueType = t.valueType));
  });
  function Qu(e, t, r) {
    return new mS({ type: "record", keyType: e, valueType: t, ...B(r) });
  }
  const Gu = C("ZodEnum", (e, t) => {
    (U1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (n, s, a) => t_(e, n, s)),
      (e.enum = t.entries),
      (e.options = Object.values(t.entries)));
    const r = new Set(Object.keys(t.entries));
    ((e.extract = (n, s) => {
      const a = {};
      for (const o of n)
        if (r.has(o)) a[o] = t.entries[o];
        else throw new Error(`Key ${o} not found in enum`);
      return new Gu({ ...t, checks: [], ...B(s), entries: a });
    }),
      (e.exclude = (n, s) => {
        const a = { ...t.entries };
        for (const o of n)
          if (r.has(o)) delete a[o];
          else throw new Error(`Key ${o} not found in enum`);
        return new Gu({ ...t, checks: [], ...B(s), entries: a });
      }));
  });
  function Tn(e, t) {
    const r = Array.isArray(e) ? Object.fromEntries(e.map((n) => [n, n])) : e;
    return new Gu({ type: "enum", entries: r, ...B(t) });
  }
  const fS = C("ZodTransform", (e, t) => {
    (Z1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => n_(e, r)),
      (e._zod.parse = (r, n) => {
        if (n.direction === "backward") throw new Yh(e.constructor.name);
        r.addIssue = (a) => {
          if (typeof a == "string") r.issues.push(Ua(a, r.value, t));
          else {
            const o = a;
            (o.fatal && (o.continue = !1),
              o.code ?? (o.code = "custom"),
              o.input ?? (o.input = r.value),
              o.inst ?? (o.inst = e),
              r.issues.push(Ua(o)));
          }
        };
        const s = t.transform(r.value, r);
        return s instanceof Promise
          ? s.then((a) => ((r.value = a), r))
          : ((r.value = s), r);
      }));
  });
  function hS(e) {
    return new fS({ type: "transform", transform: e });
  }
  const Ug = C("ZodOptional", (e, t) => {
    (B1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => g_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType));
  });
  function Zg(e) {
    return new Ug({ type: "optional", innerType: e });
  }
  const gS = C("ZodNullable", (e, t) => {
    (q1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => c_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType));
  });
  function Bg(e) {
    return new gS({ type: "nullable", innerType: e });
  }
  const yS = C("ZodDefault", (e, t) => {
    (V1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => d_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType),
      (e.removeDefault = e.unwrap));
  });
  function vS(e, t) {
    return new yS({
      type: "default",
      innerType: e,
      get defaultValue() {
        return typeof t == "function" ? t() : ng(t);
      },
    });
  }
  const xS = C("ZodPrefault", (e, t) => {
    (K1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => p_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType));
  });
  function bS(e, t) {
    return new xS({
      type: "prefault",
      innerType: e,
      get defaultValue() {
        return typeof t == "function" ? t() : ng(t);
      },
    });
  }
  const qg = C("ZodNonOptional", (e, t) => {
    (W1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => u_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType));
  });
  function wS(e, t) {
    return new qg({ type: "nonoptional", innerType: e, ...B(t) });
  }
  const kS = C("ZodCatch", (e, t) => {
    (H1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => m_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType),
      (e.removeCatch = e.unwrap));
  });
  function _S(e, t) {
    return new kS({
      type: "catch",
      innerType: e,
      catchValue: typeof t == "function" ? t : () => t,
    });
  }
  const SS = C("ZodPipe", (e, t) => {
    (Q1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => f_(e, r, n, s)),
      (e.in = t.in),
      (e.out = t.out));
  });
  function Vg(e, t) {
    return new SS({ type: "pipe", in: e, out: t });
  }
  const CS = C("ZodReadonly", (e, t) => {
    (G1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => h_(e, r, n, s)),
      (e.unwrap = () => e._zod.def.innerType));
  });
  function jS(e) {
    return new CS({ type: "readonly", innerType: e });
  }
  const NS = C("ZodCustom", (e, t) => {
    (Y1.init(e, t),
      Ae.init(e, t),
      (e._zod.processJSONSchema = (r, n, s) => r_(e, r)));
  });
  function RS(e, t = {}) {
    return qk(NS, e, t);
  }
  function AS(e) {
    return Vk(e);
  }
  const ES = W({ success: _t(), ticketNumber: oe().optional() }),
    vr = [];
  typeof window < "u" &&
    (window.addEventListener("error", (e) => {
      const t = `[JS] ${e.message} at ${e.filename}:${e.lineno}`;
      vr.includes(t) || (vr.unshift(t), vr.length > 10 && vr.pop());
    }),
    window.addEventListener("unhandledrejection", (e) => {
      const t = `[Promise] ${String(e.reason)}`;
      vr.includes(t) || (vr.unshift(t), vr.length > 10 && vr.pop());
    }));
  function TS() {
    const e = window;
    if (e.__AMCP_PS_CONFIG__) return "ps";
    const t = e.__AMCP_CONFIG__?.source;
    return t === "odoo-embed"
      ? "odoo"
      : t === "shopify-embed"
        ? "shopify"
        : t === "wp-embed" || document.getElementById("amcp-root") !== null
          ? "wp"
          : "unknown";
  }
  function PS() {
    const e = Re(),
      [t, r] = pe.useState(!1),
      [n, s] = pe.useState(""),
      [a, o] = pe.useState("idle"),
      [l, c] = pe.useState(null),
      [u, d] = pe.useState(null),
      f = () => {
        (r(!0), o("idle"), s(""), c(null), d(null));
      },
      m = () => {
        r(!1);
      },
      v = async (x) => {
        if ((x.preventDefault(), !(n.trim().length < 10))) {
          o("loading");
          try {
            const b = await ae.post(
              "/v1/embed/support/report",
              {
                platform: TS(),
                message: n.trim(),
                currentUrl: window.location.href.slice(0, 500),
                errorLogs: vr.slice(0, 10),
              },
              ES
            );
            (c(b.ticketNumber ?? null), o("success"));
          } catch (b) {
            const j = b instanceof Error ? b.message : e.common.errorUnknown;
            (d(j), o("error"));
          }
        }
      };
    return i.jsxs(i.Fragment, {
      children: [
        i.jsx("button", {
          type: "button",
          onClick: f,
          title: e.support.buttonTitle,
          "aria-label": e.support.buttonTitle,
          style: {
            position: "fixed",
            bottom: 24,
            right: 24,
            width: 44,
            height: 44,
            borderRadius: "50%",
            background: "#1a73e8",
            color: "#fff",
            border: "none",
            fontSize: 20,
            fontWeight: 700,
            cursor: "pointer",
            boxShadow: "0 2px 8px rgba(0,0,0,.25)",
            zIndex: 9998,
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            lineHeight: 1,
          },
          children: "?",
        }),
        t &&
          i.jsx("div", {
            role: "dialog",
            "aria-modal": "true",
            "aria-label": e.support.modalTitle,
            style: {
              position: "fixed",
              inset: 0,
              zIndex: 9999,
              display: "flex",
              alignItems: "flex-end",
              justifyContent: "flex-end",
              padding: 24,
              background: "rgba(0,0,0,.35)",
            },
            onClick: (x) => {
              x.target === x.currentTarget && m();
            },
            children: i.jsxs("div", {
              style: {
                background: "#fff",
                borderRadius: 12,
                boxShadow: "0 8px 32px rgba(0,0,0,.2)",
                width: 360,
                maxWidth: "calc(100vw - 48px)",
                padding: 24,
              },
              children: [
                i.jsxs("div", {
                  style: {
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    marginBottom: 16,
                  },
                  children: [
                    i.jsx("h2", {
                      style: { margin: 0, fontSize: "1rem", fontWeight: 700 },
                      children: e.support.modalTitle,
                    }),
                    i.jsx("button", {
                      type: "button",
                      onClick: m,
                      "aria-label": e.common.close,
                      style: {
                        background: "none",
                        border: "none",
                        fontSize: 20,
                        cursor: "pointer",
                        color: "#555",
                        lineHeight: 1,
                        padding: "2px 6px",
                      },
                      children: "×",
                    }),
                  ],
                }),
                a === "success" &&
                  i.jsxs("div", {
                    style: { textAlign: "center", padding: "16px 0" },
                    children: [
                      i.jsx("div", { style: { fontSize: 40 }, children: "✅" }),
                      i.jsx("p", {
                        style: {
                          fontWeight: 700,
                          margin: "12px 0 4px",
                          color: "#1a7f4f",
                        },
                        children: e.support.successTitle,
                      }),
                      l &&
                        i.jsxs("p", {
                          style: { fontSize: ".85rem", color: "#555" },
                          children: [
                            e.support.ticketLabel,
                            " ",
                            i.jsxs("strong", { children: ["#", l] }),
                          ],
                        }),
                      i.jsx("p", {
                        style: {
                          fontSize: ".8rem",
                          color: "#888",
                          marginTop: 8,
                        },
                        children: e.support.successHint,
                      }),
                      i.jsx("button", {
                        type: "button",
                        onClick: m,
                        style: {
                          marginTop: 16,
                          background: "#1a73e8",
                          color: "#fff",
                          border: "none",
                          borderRadius: 6,
                          padding: "8px 20px",
                          fontSize: ".9rem",
                          cursor: "pointer",
                          fontWeight: 600,
                        },
                        children: e.common.close,
                      }),
                    ],
                  }),
                a !== "success" &&
                  i.jsxs("form", {
                    onSubmit: (x) => {
                      v(x);
                    },
                    children: [
                      i.jsx("p", {
                        style: {
                          fontSize: ".875rem",
                          color: "#555",
                          margin: "0 0 12px",
                        },
                        children: e.support.description,
                      }),
                      i.jsx("textarea", {
                        value: n,
                        onChange: (x) => s(x.target.value),
                        placeholder: e.support.placeholder,
                        rows: 5,
                        maxLength: 2e3,
                        required: !0,
                        style: {
                          width: "100%",
                          boxSizing: "border-box",
                          border: "1px solid #ccc",
                          borderRadius: 6,
                          padding: "8px 10px",
                          fontSize: ".875rem",
                          resize: "vertical",
                          fontFamily: "inherit",
                        },
                      }),
                      i.jsx("p", {
                        style: {
                          fontSize: ".75rem",
                          color: "#888",
                          margin: "4px 0 12px",
                        },
                        children: e.support.autoLogsNote,
                      }),
                      a === "error" &&
                        u &&
                        i.jsx("p", {
                          role: "alert",
                          style: {
                            color: "#c0392b",
                            fontSize: ".8rem",
                            margin: "0 0 10px",
                          },
                          children: u,
                        }),
                      i.jsxs("div", {
                        style: {
                          display: "flex",
                          gap: 8,
                          justifyContent: "flex-end",
                        },
                        children: [
                          i.jsx("button", {
                            type: "button",
                            onClick: m,
                            disabled: a === "loading",
                            style: {
                              background: "none",
                              border: "1px solid #ccc",
                              borderRadius: 6,
                              padding: "7px 16px",
                              fontSize: ".875rem",
                              cursor: "pointer",
                            },
                            children: e.common.cancel,
                          }),
                          i.jsx("button", {
                            type: "submit",
                            disabled: a === "loading" || n.trim().length < 10,
                            style: {
                              background:
                                a === "loading" || n.trim().length < 10
                                  ? "#a0c4f1"
                                  : "#1a73e8",
                              color: "#fff",
                              border: "none",
                              borderRadius: 6,
                              padding: "7px 18px",
                              fontSize: ".875rem",
                              fontWeight: 600,
                              cursor:
                                a === "loading" || n.trim().length < 10
                                  ? "not-allowed"
                                  : "pointer",
                            },
                            children:
                              a === "loading"
                                ? e.support.sending
                                : e.support.send,
                          }),
                        ],
                      }),
                    ],
                  }),
              ],
            }),
          }),
      ],
    });
  }
  const OS = new bx({
      defaultOptions: {
        queries: {
          retry: (e, t) => {
            if (t instanceof Du && t.status === 503) return !1;
            const r = t;
            return typeof r.status == "number" &&
              [401, 403, 503].includes(r.status)
              ? !1
              : e < 1;
          },
          staleTime: 6e4,
        },
      },
    }),
    Yu = (e) =>
      e === "payment-methods" ||
      e === "settings" ||
      e === "merchant-center" ||
      e === "inicio" ||
      e === "mis-ventas" ||
      e === "mis-reglas" ||
      e === "seguridad" ||
      e === "agentes"
        ? e
        : "trust-center",
    IS = (e) => {
      switch (e) {
        case "inicio":
          return Te(() => Promise.resolve().then(() => US), void 0);
        case "payment-methods":
          return Te(() => Promise.resolve().then(() => Gg), void 0);
        case "settings":
          return Te(() => Promise.resolve().then(() => YS), void 0);
        case "merchant-center":
          return Te(() => Promise.resolve().then(() => uC), void 0);
        case "mis-ventas":
          return Te(() => Promise.resolve().then(() => CC), void 0);
        case "mis-reglas":
          return Te(() => Promise.resolve().then(() => HC), void 0);
        case "seguridad":
          return Te(() => Promise.resolve().then(() => rj), void 0);
        case "agentes":
          return Te(() => Promise.resolve().then(() => ay), void 0);
        default:
          return Te(() => Promise.resolve().then(() => cj), void 0);
      }
    },
    LS = ({ initialSection: e, onRegisterNavigate: t }) => {
      const [r, n] = pe.useState(
        e ?? Yu(document.getElementById("amcp-root")?.dataset.section)
      );
      pe.useEffect(() => {
        t?.(n);
      }, [t]);
      const s = pe.lazy(() => IS(r));
      return i.jsxs(i.Fragment, {
        children: [
          i.jsx(pe.Suspense, {
            fallback: i.jsx("div", {
              className: "p-4 text-sm text-gray-500",
              children: "Loading...",
            }),
            children: i.jsx(s, {}),
          }),
          i.jsx(PS, {}),
        ],
      });
    },
    DS = (e) => {
      if (e === void 0 || e === "") return;
      const t = e.split(/[-_]/)[0]?.toLowerCase();
      if (t === "es") return "es";
      if (t === "en") return "en";
    },
    Kg = (e, t) => {
      const r = t?.section !== void 0 ? Yu(t.section) : void 0,
        n = DS(t?.locale);
      (t?.getToken !== void 0 || t?.source !== void 0) &&
        Bb({
          source: t.source ?? "wp-embed",
          baseUrl: t.apiBase,
          getToken: t.getToken ?? (() => Promise.resolve(null)),
        });
      let s = null;
      const a = (l) => {
        s?.(Yu(l));
      };
      Ff(e).render(
        i.jsx(pe.StrictMode, {
          children: i.jsx(Kb, {
            locale: n,
            children: i.jsx(wx, {
              client: OS,
              children: i.jsx(LS, {
                initialSection: r,
                onRegisterNavigate: (l) => {
                  s = l;
                },
              }),
            }),
          }),
        })
      );
      const o = window;
      o.TrusteedEmbed.navigateSection = a;
    };
  window.TrusteedEmbed = { mount: Kg };
  const Ju = document.getElementById("amcp-root");
  Ju !== null && Ju.dataset.autoMount !== "false" && Kg(Ju);
  const zS = W({ activated: _t(), kid: oe() }),
    MS = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    Wg = (e) => {
      try {
        return MS.format(new Date(e));
      } catch {
        return e;
      }
    },
    $S = (e, t, r) =>
      t !== "ready" || e === null
        ? {
            emoji: "⏳",
            color: "#8c9196",
            bg: "#f6f6f7",
            label: r.inicio.scoreLabelComputing,
            desc: r.inicio.scoreDescComputing,
          }
        : e >= 80
          ? {
              emoji: "🌟",
              color: "#1a7f4f",
              bg: "#e3f5ec",
              label: r.inicio.scoreLabelExcellent,
              desc: r.inicio.scoreDescExcellent,
            }
          : e >= 50
            ? {
                emoji: "👍",
                color: "#b27400",
                bg: "#fff8ec",
                label: r.inicio.scoreLabelAcceptable,
                desc: r.inicio.scoreDescAcceptable,
              }
            : {
                emoji: "⚠️",
                color: "#c0392b",
                bg: "#fff0ee",
                label: r.inicio.scoreLabelNeedsAttention,
                desc: r.inicio.scoreDescNeedsAttention,
              },
    FS = ({ ariaLabel: e }) =>
      i.jsx("div", {
        className: "h-28 animate-pulse rounded-lg bg-gray-100",
        role: "status",
        "aria-label": e,
      }),
    Xu = (e) => {
      const t = window.TrusteedEmbed;
      if (t?.navigateSection) {
        t.navigateSection(e);
        return;
      }
      const r = new URL(window.location.href);
      (r.searchParams.set("page", `amcp-${e}`),
        (window.location.href = r.toString()));
    },
    US = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              t = {
                checkout: e.inicio.bucketCheckout,
                customer: e.inicio.bucketCustomer,
                discovery: e.inicio.bucketDiscovery,
              },
              {
                data: r,
                isLoading: n,
                isError: s,
                error: a,
              } = _e({
                queryKey: ["trust-overview-inicio"],
                queryFn: () => ae.get("/v1/embed/trust/overview", Oh),
              }),
              { data: o, isLoading: l } = _e({
                queryKey: ["trust-keys-inicio"],
                queryFn: async () => {
                  try {
                    return await ae.get("/v1/embed/trust/keys", Fa);
                  } catch (j) {
                    if (j instanceof Bt && j.status === 404) return null;
                    throw j;
                  }
                },
                retry: !1,
              }),
              c = fr(),
              u = Pt({
                mutationFn: () =>
                  ae.post("/v1/embed/trust/keys/activate", {}, zS),
                onSuccess: () => {
                  (c.invalidateQueries({ queryKey: ["trust-keys-inicio"] }),
                    c.invalidateQueries({
                      queryKey: ["trust-overview-inicio"],
                    }));
                },
              }),
              d = r?.score?.status === "ready" ? r.score.score : null,
              f = r?.score?.status ?? "pending",
              m = r?.recentReceipts ?? [],
              v = o ?? null,
              x = r?.computedAt ?? null,
              b = $S(d, f, e);
            return i.jsxs("div", {
              className: "max-w-4xl space-y-6 p-6",
              children: [
                i.jsx("h1", {
                  className: "text-xl font-semibold text-gray-900",
                  children: e.inicio.title,
                }),
                s &&
                  i.jsxs("div", {
                    className:
                      "rounded border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800",
                    role: "alert",
                    children: [
                      e.inicio.errorLoadingSummary,
                      " ",
                      a instanceof Error ? a.message : e.common.errorUnknown,
                    ],
                  }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: i.jsx("h2", {
                        className: "font-semibold text-gray-900",
                        children: e.inicio.reputationSection,
                      }),
                    }),
                    i.jsx("div", {
                      className: "p-6",
                      children: n
                        ? i.jsx(FS, { ariaLabel: e.inicio.scoreAriaLoading })
                        : i.jsxs("div", {
                            className: "space-y-4",
                            children: [
                              i.jsxs("div", {
                                className: "flex flex-wrap items-center gap-5",
                                children: [
                                  i.jsxs("div", {
                                    style: {
                                      width: 96,
                                      height: 96,
                                      background: b.bg,
                                      border: `4px solid ${b.color}`,
                                    },
                                    className:
                                      "flex shrink-0 flex-col items-center justify-center rounded-full",
                                    children: [
                                      i.jsx("span", {
                                        style: {
                                          fontSize: 28,
                                          fontWeight: 800,
                                          color: b.color,
                                          lineHeight: 1,
                                        },
                                        children: f === "ready" ? d : "—",
                                      }),
                                      i.jsx("span", {
                                        style: {
                                          fontSize: 10,
                                          color: b.color,
                                          fontWeight: 600,
                                        },
                                        children: f === "ready" ? "/ 100" : "…",
                                      }),
                                    ],
                                  }),
                                  i.jsxs("div", {
                                    className: "flex-1",
                                    children: [
                                      i.jsxs("div", {
                                        className:
                                          "mb-1 flex items-center gap-2",
                                        children: [
                                          i.jsx("span", {
                                            className: "text-xl",
                                            children: b.emoji,
                                          }),
                                          i.jsx("span", {
                                            style: { color: b.color },
                                            className: "text-lg font-bold",
                                            children: b.label,
                                          }),
                                        ],
                                      }),
                                      i.jsx("p", {
                                        className: "text-sm text-gray-500",
                                        children: b.desc,
                                      }),
                                      x &&
                                        i.jsx("p", {
                                          className:
                                            "mt-1 text-xs text-gray-400",
                                          children: e.inicio.updatedAt.replace(
                                            "{{date}}",
                                            Wg(x)
                                          ),
                                        }),
                                    ],
                                  }),
                                ],
                              }),
                              f === "ready" &&
                                d !== null &&
                                i.jsxs("div", {
                                  children: [
                                    i.jsx("div", {
                                      className: "relative h-2.5 rounded-full",
                                      style: {
                                        background:
                                          "linear-gradient(to right, #fac9c3 0%, #ffd79d 40%, #95c9a8 70%, #1a7f4f 100%)",
                                      },
                                      children: i.jsx("div", {
                                        className:
                                          "absolute top-[-3px] h-4 w-4 rounded-full border-2 bg-white shadow",
                                        style: {
                                          left: `calc(${d}% - 8px)`,
                                          borderColor: b.color,
                                        },
                                      }),
                                    }),
                                    i.jsxs("div", {
                                      className:
                                        "mt-1 flex justify-between text-xs text-gray-400",
                                      children: [
                                        i.jsx("span", {
                                          children: e.inicio.scoreLow,
                                        }),
                                        i.jsx("span", {
                                          children: e.inicio.scoreMid,
                                        }),
                                        i.jsx("span", {
                                          children: e.inicio.scoreHigh,
                                        }),
                                      ],
                                    }),
                                  ],
                                }),
                              i.jsxs("div", {
                                className: "flex flex-wrap gap-3 pt-2",
                                children: [
                                  i.jsx("button", {
                                    onClick: () => Xu("trust-center"),
                                    className:
                                      "rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800",
                                    children: e.inicio.manageReputation,
                                  }),
                                  i.jsx("button", {
                                    onClick: () => Xu("seguridad"),
                                    className:
                                      "rounded-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-100",
                                    children: e.inicio.viewActivity,
                                  }),
                                ],
                              }),
                            ],
                          }),
                    }),
                  ],
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: i.jsx("h2", {
                        className: "font-semibold text-gray-900",
                        children: e.inicio.securitySection,
                      }),
                    }),
                    i.jsx("div", {
                      className: "p-6",
                      children:
                        n || l
                          ? i.jsx("div", {
                              className:
                                "h-16 animate-pulse rounded-lg bg-gray-100",
                            })
                          : v
                            ? i.jsxs("div", {
                                className: "flex items-center gap-4",
                                children: [
                                  i.jsxs("svg", {
                                    width: "52",
                                    height: "60",
                                    viewBox: "0 0 52 60",
                                    fill: "none",
                                    "aria-hidden": "true",
                                    children: [
                                      i.jsx("path", {
                                        d: "M26 2L4 11v16c0 14.4 9.4 27.8 22 31.6C38.6 54.8 48 41.4 48 27V11L26 2z",
                                        fill: "#e3f5ec",
                                        stroke: "#1a7f4f",
                                        strokeWidth: "2.5",
                                        strokeLinejoin: "round",
                                      }),
                                      i.jsx("path", {
                                        d: "M16 30l7 7 13-14",
                                        stroke: "#1a7f4f",
                                        strokeWidth: "3",
                                        strokeLinecap: "round",
                                        strokeLinejoin: "round",
                                      }),
                                    ],
                                  }),
                                  i.jsxs("div", {
                                    children: [
                                      i.jsx("span", {
                                        className:
                                          "inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 mb-1",
                                        children: e.inicio.securityProtected,
                                      }),
                                      i.jsx("p", {
                                        className: "text-sm text-gray-500",
                                        children:
                                          e.inicio.securityProtectedDesc,
                                      }),
                                      v.activeKid &&
                                        i.jsxs("p", {
                                          className:
                                            "mt-1 text-xs text-gray-400",
                                          children: [
                                            e.inicio.securityActiveKey.replace(
                                              "{{kid}}",
                                              v.activeKid
                                            ),
                                            (v.retiredGraceKids?.length ?? 0) >
                                              0 &&
                                              ` · ${e.inicio.securityGraceKeys.replace("{{count}}", String(v.retiredGraceKids.length))}`,
                                          ],
                                        }),
                                    ],
                                  }),
                                ],
                              })
                            : i.jsxs("div", {
                                className: "flex items-center gap-4",
                                children: [
                                  i.jsxs("svg", {
                                    width: "52",
                                    height: "60",
                                    viewBox: "0 0 52 60",
                                    fill: "none",
                                    "aria-hidden": "true",
                                    children: [
                                      i.jsx("path", {
                                        d: "M26 2L4 11v16c0 14.4 9.4 27.8 22 31.6C38.6 54.8 48 41.4 48 27V11L26 2z",
                                        fill: "#fff8ec",
                                        stroke: "#b27400",
                                        strokeWidth: "2.5",
                                        strokeLinejoin: "round",
                                      }),
                                      i.jsx("path", {
                                        d: "M26 20v12M26 36v2",
                                        stroke: "#b27400",
                                        strokeWidth: "3",
                                        strokeLinecap: "round",
                                      }),
                                    ],
                                  }),
                                  i.jsxs("div", {
                                    children: [
                                      i.jsx("span", {
                                        className:
                                          "inline-block rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700 mb-1",
                                        children: e.inicio.securityNotActive,
                                      }),
                                      i.jsx("p", {
                                        className: "text-sm text-gray-500",
                                        children:
                                          e.inicio.securityNotActiveDesc,
                                      }),
                                      i.jsx("button", {
                                        onClick: () => u.mutate(),
                                        disabled: u.isPending,
                                        className:
                                          "mt-2 rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 disabled:opacity-60",
                                        children: u.isPending
                                          ? e.common.loading
                                          : e.inicio.securityActivate,
                                      }),
                                      u.isError &&
                                        i.jsx("p", {
                                          className:
                                            "mt-2 text-xs text-red-600",
                                          role: "alert",
                                          children:
                                            u.error instanceof Error
                                              ? u.error.message
                                              : e.common.errorUnknown,
                                        }),
                                    ],
                                  }),
                                ],
                              }),
                    }),
                  ],
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: i.jsx("h2", {
                        className: "font-semibold text-gray-900",
                        children: e.inicio.salesSection,
                      }),
                    }),
                    i.jsxs("div", {
                      className: "p-6",
                      children: [
                        n
                          ? i.jsx("div", {
                              className:
                                "h-24 animate-pulse rounded-lg bg-gray-100",
                            })
                          : m.length === 0
                            ? i.jsxs("div", {
                                className: "flex items-center gap-4",
                                children: [
                                  i.jsx("div", {
                                    className:
                                      "flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-2xl text-gray-400",
                                    children: "$",
                                  }),
                                  i.jsxs("div", {
                                    children: [
                                      i.jsx("p", {
                                        className:
                                          "font-semibold text-gray-800",
                                        children: e.inicio.salesEmpty,
                                      }),
                                      i.jsx("p", {
                                        className: "text-sm text-gray-500",
                                        children: e.inicio.salesEmptyDesc,
                                      }),
                                    ],
                                  }),
                                ],
                              })
                            : i.jsx("div", {
                                className: "overflow-x-auto",
                                children: i.jsxs("table", {
                                  className: "min-w-full text-sm",
                                  "aria-label": e.inicio.salesTableLabel,
                                  children: [
                                    i.jsx("thead", {
                                      children: i.jsxs("tr", {
                                        className:
                                          "border-b border-gray-200 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                                        children: [
                                          i.jsx("th", {
                                            className: "px-4 py-3",
                                            children: e.inicio.salesColNumber,
                                          }),
                                          i.jsx("th", {
                                            className: "px-4 py-3",
                                            children: e.inicio.salesColType,
                                          }),
                                          i.jsx("th", {
                                            className: "px-4 py-3",
                                            children: e.inicio.salesColBuyer,
                                          }),
                                          i.jsx("th", {
                                            className: "px-4 py-3",
                                            children: e.inicio.salesColDate,
                                          }),
                                        ],
                                      }),
                                    }),
                                    i.jsx("tbody", {
                                      className: "divide-y divide-gray-100",
                                      children: m.map((j) =>
                                        i.jsxs(
                                          "tr",
                                          {
                                            className: "hover:bg-gray-50",
                                            children: [
                                              i.jsxs("td", {
                                                className:
                                                  "px-4 py-3 font-mono text-xs text-gray-400",
                                                children: [
                                                  "#",
                                                  j.id.slice(0, 8),
                                                ],
                                              }),
                                              i.jsx("td", {
                                                className: "px-4 py-3",
                                                children: i.jsx("span", {
                                                  className:
                                                    "rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700",
                                                  children:
                                                    t[j.bucket] ?? j.bucket,
                                                }),
                                              }),
                                              i.jsxs("td", {
                                                className:
                                                  "px-4 py-3 font-mono text-xs text-gray-400",
                                                children: [
                                                  "#",
                                                  j.agentId.slice(0, 12),
                                                ],
                                              }),
                                              i.jsx("td", {
                                                className:
                                                  "px-4 py-3 text-gray-500",
                                                children: Wg(j.createdAt),
                                              }),
                                            ],
                                          },
                                          j.id
                                        )
                                      ),
                                    }),
                                  ],
                                }),
                              }),
                        i.jsx("div", {
                          className: "mt-4",
                          children: i.jsx("button", {
                            onClick: () => Xu("mis-ventas"),
                            className:
                              "rounded-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-100",
                            children: e.inicio.viewAllOrders,
                          }),
                        }),
                      ],
                    }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    ZS = W({
      id: oe(),
      name: oe(),
      category: oe(),
      maturity: Tn(["GA", "COMING_SOON"]),
      status: oe(),
      enabled: _t(),
      connectFlow: oe(),
      description: oe(),
      lastHealthAt: oe().nullable(),
      lastError: oe().nullable(),
    }),
    BS = W({ items: He(ZS) }),
    Hg = Fg(),
    qS = {
      CONECTADO: "bg-green-100 text-green-700",
      PENDIENTE: "bg-yellow-100 text-yellow-700",
      ERROR: "bg-red-100 text-red-700",
      DESACTIVADO: "bg-gray-100 text-gray-500",
      DISPONIBLE: "bg-gray-100 text-gray-500",
    },
    VS = () => {
      const [e, t] = R.useState([]),
        r = R.useRef(0),
        n = R.useCallback((a, o) => {
          const l = ++r.current;
          (t((c) => [...c, { id: l, message: a, type: o }]),
            setTimeout(() => t((c) => c.filter((u) => u.id !== l)), 5e3));
        }, []),
        s = R.useCallback((a) => t((o) => o.filter((l) => l.id !== a)), []);
      return { toasts: e, add: n, remove: s };
    },
    Qg = ({ methodId: e, connectFlow: t, onDone: r, onError: n, t: s }) => {
      const [a, o] = R.useState(""),
        l = fr(),
        c = Pt({
          mutationFn: () => {
            const u =
              t === "apiKey"
                ? { flow: "apiKey", apiKey: a }
                : { flow: "wallet", walletAddress: a };
            return ae.post(
              `/v1/embed/merchant/payment-methods/${encodeURIComponent(e)}/connect`,
              u,
              Hg
            );
          },
          onSuccess: () => {
            (o(""), l.invalidateQueries({ queryKey: ["pm-catalog"] }), r());
          },
          onError: (u) => {
            n(u instanceof Error ? u.message : s.common.error);
          },
        });
      return i.jsxs("form", {
        onSubmit: (u) => {
          (u.preventDefault(), a.trim() && c.mutate());
        },
        className: "mt-3 flex gap-2",
        children: [
          i.jsx("input", {
            type: t === "apiKey" ? "password" : "text",
            value: a,
            onChange: (u) => o(u.target.value),
            placeholder:
              t === "apiKey"
                ? s.merchantPaymentMethods.connectApiKey
                : s.merchantPaymentMethods.connectWallet,
            required: !0,
            className:
              "min-w-0 flex-1 rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
            "aria-label":
              t === "apiKey"
                ? s.merchantPaymentMethods.connectApiKey
                : s.merchantPaymentMethods.connectWallet,
          }),
          i.jsx("button", {
            type: "submit",
            disabled: c.isPending || !a.trim(),
            className:
              "shrink-0 rounded bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60",
            children: c.isPending
              ? s.merchantPaymentMethods.connecting
              : s.merchantPaymentMethods.connectBtn,
          }),
        ],
      });
    },
    KS = ({ method: e, onSuccess: t, onError: r, t: n }) => {
      const s = fr(),
        a = (d) => ({
          mutationFn: () =>
            ae.post(
              `/v1/embed/merchant/payment-methods/${encodeURIComponent(e.id)}/${d}`,
              {},
              Hg
            ),
          onSuccess: () => {
            s.invalidateQueries({ queryKey: ["pm-catalog"] });
            const f = {
              disconnect: n.merchantPaymentMethods.actionDisconnected,
              enable: n.merchantPaymentMethods.actionEnabled,
              disable: n.merchantPaymentMethods.actionDisabled,
            };
            t(f[d] ?? n.merchantPaymentMethods.actionConnected);
          },
          onError: (f) => {
            r(
              f instanceof Error
                ? f.message
                : n.merchantPaymentMethods.actionError
            );
          },
        }),
        o = Pt(a("disconnect")),
        l = Pt(a("enable")),
        c = Pt(a("disable")),
        u = o.isPending || l.isPending || c.isPending;
      return e.status === "DISPONIBLE"
        ? i.jsx(Qg, {
            methodId: e.id,
            connectFlow: e.connectFlow,
            onDone: () => t(n.merchantPaymentMethods.actionConnected),
            onError: r,
            t: n,
          })
        : e.status === "ERROR"
          ? i.jsxs(i.Fragment, {
              children: [
                e.lastError &&
                  i.jsx("p", {
                    className: "mt-2 text-xs text-red-600",
                    children: e.lastError,
                  }),
                i.jsx(Qg, {
                  methodId: e.id,
                  connectFlow: e.connectFlow,
                  onDone: () => t(n.merchantPaymentMethods.actionReconnected),
                  onError: r,
                  t: n,
                }),
              ],
            })
          : i.jsxs("div", {
              className: "mt-3 flex flex-wrap gap-2",
              children: [
                e.status === "DESACTIVADO" &&
                  i.jsx("button", {
                    onClick: () => l.mutate(),
                    disabled: u,
                    className:
                      "rounded border border-green-300 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-50 disabled:opacity-60",
                    children: n.merchantPaymentMethods.enableBtn,
                  }),
                e.status === "CONECTADO" &&
                  i.jsx("button", {
                    onClick: () => c.mutate(),
                    disabled: u,
                    className:
                      "rounded border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-60",
                    children: n.merchantPaymentMethods.disableBtn,
                  }),
                i.jsx("button", {
                  onClick: () => o.mutate(),
                  disabled: u,
                  className:
                    "rounded border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 disabled:opacity-60",
                  children: n.merchantPaymentMethods.disconnectBtn,
                }),
              ],
            });
    },
    WS = ({ method: e, onSuccess: t, onError: r, t: n }) => {
      const s = {
          CONECTADO: n.merchantPaymentMethods.statusConnected,
          PENDIENTE: n.merchantPaymentMethods.statusVerifying,
          ERROR: n.merchantPaymentMethods.statusError,
          DESACTIVADO: n.merchantPaymentMethods.statusDisabled,
          DISPONIBLE: n.merchantPaymentMethods.statusAvailable,
        },
        a = qS[e.status] ?? "bg-gray-100 text-gray-500",
        o = s[e.status] ?? e.status;
      return i.jsxs("div", {
        className: "rounded-lg border border-gray-200 bg-white p-4 shadow-sm",
        children: [
          i.jsxs("div", {
            className: "flex items-start justify-between gap-2",
            children: [
              i.jsxs("div", {
                className: "min-w-0",
                children: [
                  i.jsx("p", {
                    className: "truncate font-medium text-gray-900",
                    children: e.name,
                  }),
                  i.jsx("p", {
                    className: "mt-0.5 text-xs text-gray-400",
                    children:
                      n.merchantPaymentMethods.methodDescriptions[e.id] ??
                      e.description,
                  }),
                  e.lastHealthAt &&
                    i.jsx("p", {
                      className: "mt-1 text-xs text-gray-400",
                      children: n.merchantPaymentMethods.verifiedAt.replace(
                        "{{date}}",
                        new Intl.DateTimeFormat("es-ES", {
                          dateStyle: "short",
                          timeStyle: "short",
                          timeZone: "UTC",
                        }).format(new Date(e.lastHealthAt))
                      ),
                    }),
                ],
              }),
              i.jsx("span", {
                className: `shrink-0 rounded-full px-2 py-0.5 text-xs font-medium ${a}`,
                children: o,
              }),
            ],
          }),
          i.jsx(KS, { method: e, onSuccess: t, onError: r, t: n }),
        ],
      });
    },
    HS = () =>
      i.jsx("div", { className: "h-28 animate-pulse rounded-lg bg-gray-100" }),
    Gg = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              { toasts: t, add: r, remove: n } = VS(),
              {
                data: s,
                isLoading: a,
                isError: o,
                error: l,
              } = _e({
                queryKey: ["pm-catalog"],
                queryFn: () =>
                  ae.get("/v1/embed/merchant/payment-methods/catalog", BS),
              }),
              c = (s?.items ?? []).filter((d) => d.maturity === "GA"),
              u = (s?.items ?? []).filter((d) => d.maturity === "COMING_SOON");
            return i.jsxs("div", {
              className: "p-6 max-w-3xl",
              children: [
                i.jsx("div", {
                  className: "fixed bottom-4 right-4 z-50 flex flex-col gap-2",
                  children: t.map((d) =>
                    i.jsxs(
                      "div",
                      {
                        role: "status",
                        "aria-live": "polite",
                        className: `flex items-center justify-between rounded-lg px-4 py-3 text-sm shadow-md ${d.type === "success" ? "bg-green-600 text-white" : "bg-red-600 text-white"}`,
                        children: [
                          i.jsx("span", { children: d.message }),
                          i.jsx("button", {
                            onClick: () => n(d.id),
                            className: "ml-4 opacity-80 hover:opacity-100",
                            "aria-label": e.merchantPaymentMethods.closeBtn,
                            children: "✕",
                          }),
                        ],
                      },
                      d.id
                    )
                  ),
                }),
                i.jsx("p", {
                  className: "mb-5 text-sm text-gray-500",
                  children: e.merchantPaymentMethods.description,
                }),
                o &&
                  i.jsxs("div", {
                    className:
                      "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      e.merchantPaymentMethods.errorLoading,
                      " ",
                      l instanceof Error ? l.message : e.common.errorUnknown,
                    ],
                  }),
                (a || c.length > 0) &&
                  i.jsxs("section", {
                    className: "mb-8",
                    children: [
                      i.jsx("h3", {
                        className:
                          "mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500",
                        children: e.merchantPaymentMethods.sectionAvailable,
                      }),
                      i.jsxs("div", {
                        className: "grid gap-4 sm:grid-cols-2",
                        children: [
                          a &&
                            Array.from({ length: 4 }).map((d, f) =>
                              i.jsx(HS, {}, f)
                            ),
                          !a &&
                            c.map((d) =>
                              i.jsx(
                                WS,
                                {
                                  method: d,
                                  onSuccess: (f) => r(f, "success"),
                                  onError: (f) => r(f, "error"),
                                  t: e,
                                },
                                d.id
                              )
                            ),
                        ],
                      }),
                    ],
                  }),
                !a &&
                  u.length > 0 &&
                  i.jsxs("section", {
                    children: [
                      i.jsx("h3", {
                        className:
                          "mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500",
                        children: e.merchantPaymentMethods.sectionComingSoon,
                      }),
                      i.jsx("div", {
                        className: "grid gap-4 sm:grid-cols-2",
                        children: u.map((d) =>
                          i.jsxs(
                            "div",
                            {
                              className:
                                "rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 opacity-70",
                              children: [
                                i.jsx("p", {
                                  className: "font-medium text-gray-700",
                                  children: d.name,
                                }),
                                i.jsx("p", {
                                  className: "mt-0.5 text-xs text-gray-400",
                                  children:
                                    e.merchantPaymentMethods.methodDescriptions[
                                      d.id
                                    ] ?? d.description,
                                }),
                                i.jsx("p", {
                                  className: "mt-2 text-xs text-gray-400",
                                  children:
                                    e.merchantPaymentMethods.comingSoonLabel,
                                }),
                              ],
                            },
                            d.id
                          )
                        ),
                      }),
                    ],
                  }),
                !a &&
                  c.length === 0 &&
                  u.length === 0 &&
                  !o &&
                  i.jsx("p", {
                    className: "text-sm text-gray-500",
                    children: e.merchantPaymentMethods.noMethods,
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    QS = W({
      fallbackMode: Tn(["strict", "balanced", "permissive"]).default(
        "balanced"
      ),
    }),
    GS = () =>
      i.jsx("div", {
        className: "space-y-3",
        children: Array.from({ length: 3 }).map((e, t) =>
          i.jsx(
            "div",
            { className: "h-12 animate-pulse rounded-lg bg-gray-100" },
            t
          )
        ),
      }),
    YS = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              t = fr(),
              [r, n] = R.useState(null),
              [s, a] = R.useState(null),
              o = [
                {
                  value: "balanced",
                  label: e.settings.optionBalanced,
                  hint: e.settings.optionBalancedHint,
                },
                {
                  value: "strict",
                  label: e.settings.optionStrict,
                  hint: e.settings.optionStrictHint,
                },
                {
                  value: "permissive",
                  label: e.settings.optionPermissive,
                  hint: e.settings.optionPermissiveHint,
                },
              ],
              l = {
                "stripe-connect": e.settings.railStripe,
                x402: e.settings.railX402,
                acp: e.settings.railAcp,
                paypal: e.settings.railPaypal,
                eidas: e.settings.railEidas,
              },
              c = _e({
                queryKey: ["merchant-checkout-config"],
                queryFn: () => ae.get("/v1/embed/merchant/checkout/config", Fh),
              }),
              u = _e({
                queryKey: ["merchant-config"],
                queryFn: () => ae.get("/v1/embed/merchant/config", QS),
              }),
              [d, f] = R.useState(null),
              m = d ?? u.data?.fallbackMode ?? "balanced",
              v = o.find((p) => p.value === m)?.hint ?? "",
              x = Pt({
                mutationFn: (p) =>
                  ae.put(
                    "/v1/embed/merchant/config",
                    { fallbackMode: p },
                    Ba()
                  ),
                onMutate: (p) => {
                  (f(p), n(null), a(null));
                },
                onSuccess: () => {
                  (n(e.settings.configSaved),
                    t.invalidateQueries({ queryKey: ["merchant-config"] }),
                    setTimeout(() => n(null), 4e3));
                },
                onError: (p) => {
                  (a(
                    `${e.settings.errorSavingConfig} ${p instanceof Error ? p.message : e.common.errorUnknown}`
                  ),
                    f(null));
                },
              }),
              b = c.data,
              j = new Set(
                (b?.rules ?? []).filter((p) => p.enabled).map((p) => p.rail)
              ),
              g =
                (b?.railsPriority?.length ?? 0) > 0
                  ? (b?.railsPriority ?? [])
                  : (b?.rules ?? []).map((p) => p.rail);
            return i.jsxs("div", {
              className: "max-w-3xl space-y-6 p-6",
              children: [
                i.jsx("h1", {
                  className: "text-xl font-semibold text-gray-900",
                  children: e.settings.title,
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsxs("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: [
                        i.jsx("h2", {
                          className: "font-semibold text-gray-900",
                          children: e.settings.failureModeSection,
                        }),
                        i.jsx("p", {
                          className: "mt-1 text-sm text-gray-500",
                          children: e.settings.failureModeSectionDesc,
                        }),
                      ],
                    }),
                    i.jsxs("div", {
                      className: "space-y-3 p-6",
                      children: [
                        r &&
                          i.jsx("div", {
                            className:
                              "rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800",
                            children: r,
                          }),
                        s &&
                          i.jsx("div", {
                            className:
                              "rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                            role: "alert",
                            children: s,
                          }),
                        u.isLoading
                          ? i.jsx("div", {
                              className:
                                "h-10 animate-pulse rounded bg-gray-100",
                            })
                          : i.jsxs("div", {
                              className: "space-y-2",
                              children: [
                                i.jsx("select", {
                                  value: m,
                                  disabled: x.isPending,
                                  onChange: (p) => x.mutate(p.target.value),
                                  className:
                                    "w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:opacity-60",
                                  children: o.map((p) =>
                                    i.jsx(
                                      "option",
                                      { value: p.value, children: p.label },
                                      p.value
                                    )
                                  ),
                                }),
                                v &&
                                  i.jsx("p", {
                                    className: "text-xs text-gray-500",
                                    children: v,
                                  }),
                              ],
                            }),
                      ],
                    }),
                  ],
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: i.jsx("h2", {
                        className: "font-semibold text-gray-900",
                        children: e.settings.paymentSection,
                      }),
                    }),
                    i.jsxs("div", {
                      className: "p-6",
                      children: [
                        i.jsx("div", {
                          className:
                            "mb-4 rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800",
                          children: e.settings.paymentSectionNotice,
                        }),
                        c.isError &&
                          i.jsxs("div", {
                            className:
                              "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                            role: "alert",
                            children: [
                              e.settings.paymentErrorLoading,
                              " ",
                              c.error instanceof Error
                                ? c.error.message
                                : e.common.errorUnknown,
                            ],
                          }),
                        c.isLoading
                          ? i.jsx(GS, {})
                          : g.length === 0
                            ? i.jsx("div", {
                                className:
                                  "rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800",
                                children: e.settings.paymentNoRails,
                              })
                            : i.jsxs("div", {
                                className: "space-y-2",
                                children: [
                                  i.jsx("p", {
                                    className:
                                      "mb-3 text-sm font-semibold text-gray-800",
                                    children: e.settings.paymentOrder,
                                  }),
                                  g.map((p, y) => {
                                    const w = b?.rules.find(
                                        (A) => A.rail === p
                                      ),
                                      S = j.has(p);
                                    return i.jsxs(
                                      "div",
                                      {
                                        className:
                                          "flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm",
                                        children: [
                                          i.jsx("span", {
                                            className:
                                              "flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-500",
                                            children: y + 1,
                                          }),
                                          i.jsxs("div", {
                                            className: "flex-1",
                                            children: [
                                              i.jsx("p", {
                                                className:
                                                  "text-sm font-medium text-gray-800",
                                                children: l[p] ?? p,
                                              }),
                                              i.jsxs("p", {
                                                className:
                                                  "text-xs text-gray-400",
                                                children: [
                                                  S
                                                    ? e.settings
                                                        .paymentRailEnabled
                                                    : e.settings
                                                        .paymentRailDisabled,
                                                  w?.minAmount != null &&
                                                    ` · ${e.settings.paymentRailMin.replace("{{amount}}", String(w.minAmount))}`,
                                                  w?.maxAmount != null &&
                                                    ` · ${e.settings.paymentRailMax.replace("{{amount}}", String(w.maxAmount))}`,
                                                  (w?.currencyAllowlist
                                                    ?.length ?? 0) > 0 &&
                                                    ` · ${e.settings.paymentRailCurrencies.replace("{{currencies}}", w.currencyAllowlist.join(", "))}`,
                                                ],
                                              }),
                                            ],
                                          }),
                                        ],
                                      },
                                      p
                                    );
                                  }),
                                  i.jsx("p", {
                                    className: "mt-2 text-xs text-gray-400",
                                    children: e.settings.paymentChangeNote,
                                  }),
                                ],
                              }),
                      ],
                    }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    JS = W({
      id_shop: ue().int().positive(),
      allowed_shops: He(ue().int().positive()),
      all_shops: _t(),
    }),
    XS = () => ae.get("/v1/embed/merchant/shops", JS),
    eC = ({ onShopChange: e }) => {
      const t = Re(),
        {
          data: r,
          isLoading: n,
          isError: s,
        } = _e({
          queryKey: ["embed-merchant-shops"],
          queryFn: XS,
          staleTime: 300 * 1e3,
        });
      if (n)
        return i.jsx("div", {
          className: "h-8 w-36 animate-pulse rounded bg-gray-100",
        });
      if (s || !r || (!r.all_shops && r.allowed_shops.length <= 1)) return null;
      const a = r.all_shops
        ? [
            {
              id: r.id_shop,
              label: t.shopSwitcher.shopLabel.replace(
                "{{id}}",
                String(r.id_shop)
              ),
            },
          ]
        : r.allowed_shops.map((o) => ({
            id: o,
            label: t.shopSwitcher.shopLabel.replace("{{id}}", String(o)),
          }));
      return i.jsxs("div", {
        className: "flex items-center gap-2",
        children: [
          r.all_shops &&
            i.jsx("span", {
              className:
                "rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700",
              title: t.shopSwitcher.superAdminTitle,
              children: t.shopSwitcher.superAdmin,
            }),
          i.jsx("label", {
            htmlFor: "shop-switcher",
            className: "sr-only",
            children: t.shopSwitcher.changeSrOnly,
          }),
          i.jsx("select", {
            id: "shop-switcher",
            value: r.id_shop,
            onChange: (o) => e?.(Number(o.target.value)),
            className:
              "rounded border border-gray-300 bg-white px-2 py-1 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500",
            children: a.map((o) =>
              i.jsx("option", { value: o.id, children: o.label }, o.id)
            ),
          }),
        ],
      });
    },
    tC = pe.lazy(() => Te(() => Promise.resolve().then(() => gj), void 0)),
    rC = pe.lazy(() => Te(() => Promise.resolve().then(() => Gg), void 0)),
    nC = pe.lazy(() => Te(() => Promise.resolve().then(() => ay), void 0)),
    sC = pe.lazy(() => Te(() => Promise.resolve().then(() => vj), void 0)),
    aC = pe.lazy(() => Te(() => Promise.resolve().then(() => Cj), void 0)),
    oC = {
      "#orders": "orders",
      "#payments": "payments",
      "#agents": "agents",
      "#checkout": "checkout",
      "#cert-nlweb": "cert-nlweb",
    },
    iC = {
      orders: "#orders",
      payments: "#payments",
      agents: "#agents",
      checkout: "#checkout",
      "cert-nlweb": "#cert-nlweb",
    },
    Yg = (e) => oC[e] ?? "orders",
    lC = ({ active: e, onChange: t, tabs: r, ariaLabel: n }) =>
      i.jsx("nav", {
        className: "flex flex-wrap border-b border-gray-200",
        role: "tablist",
        "aria-label": n,
        children: r.map((s) =>
          i.jsx(
            "button",
            {
              role: "tab",
              "aria-selected": e === s.id,
              onClick: () => t(s.id),
              className: `px-5 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 ${e === s.id ? "border-b-2 border-blue-600 text-blue-700" : "text-gray-500 hover:text-gray-700"}`,
              children: s.label,
            },
            s.id
          )
        ),
      }),
    cC = ({ loading: e }) =>
      i.jsx("div", { className: "p-6 text-sm text-gray-500", children: e }),
    uC = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              [t, r] = R.useState(Yg(window.location.hash)),
              [n, s] = R.useState(void 0),
              a = [
                { id: "orders", label: e.merchantCenter.tabOrders },
                { id: "payments", label: e.merchantCenter.tabPayments },
                { id: "agents", label: e.merchantCenter.tabAgents },
                { id: "checkout", label: e.merchantCenter.tabCheckout },
                { id: "cert-nlweb", label: e.merchantCenter.tabCertNlweb },
              ];
            R.useEffect(() => {
              const c = () => {
                r(Yg(window.location.hash));
              };
              return (
                window.addEventListener("hashchange", c),
                () => window.removeEventListener("hashchange", c)
              );
            }, []);
            const o = R.useCallback((c) => {
                (r(c), window.history.replaceState(null, "", iC[c]));
              }, []),
              l = R.useCallback((c) => {
                s(c);
              }, []);
            return i.jsxs("div", {
              className: "max-w-5xl",
              children: [
                i.jsxs("div", {
                  className: "mb-2 flex items-center justify-between",
                  children: [
                    i.jsx("h1", {
                      className: "text-lg font-semibold text-gray-900",
                      children: e.merchantCenter.title,
                    }),
                    i.jsx(eC, { onShopChange: l }),
                  ],
                }),
                n !== void 0 &&
                  i.jsx("div", {
                    className:
                      "mb-2 rounded bg-blue-50 px-3 py-1.5 text-xs text-blue-700",
                    children: e.merchantCenter.showingStore.replace(
                      "{{id}}",
                      String(n)
                    ),
                  }),
                i.jsx(lC, {
                  active: t,
                  onChange: o,
                  tabs: a,
                  ariaLabel: e.merchantCenter.tabsAriaLabel,
                }),
                i.jsxs(R.Suspense, {
                  fallback: i.jsx(cC, { loading: e.merchantCenter.loading }),
                  children: [
                    t === "orders" && i.jsx(tC, {}),
                    t === "payments" && i.jsx(rC, {}),
                    t === "agents" && i.jsx(nC, {}),
                    t === "checkout" && i.jsx(sC, {}),
                    t === "cert-nlweb" && i.jsx(aC, {}),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    dC = pe.lazy(() => Te(() => Promise.resolve().then(() => Tj), void 0)),
    pC = pe.lazy(() => Te(() => Promise.resolve().then(() => $j), void 0)),
    mC = pe.lazy(() => Te(() => Promise.resolve().then(() => Bj), void 0)),
    fC = pe.lazy(() => Te(() => Promise.resolve().then(() => Xj), void 0)),
    hC = { "#ventas-ia": "receipts", "#claves": "keys", "#auditoria": "audit" },
    gC = {
      orders: "",
      receipts: "#ventas-ia",
      keys: "#claves",
      audit: "#auditoria",
    },
    Jg = (e) => hC[e] ?? "orders",
    yC = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    vC = (e) => {
      try {
        return yC.format(new Date(e));
      } catch {
        return e;
      }
    },
    xC = {
      SHOPIFY: "bg-blue-100 text-blue-700",
      WOO: "bg-orange-100 text-orange-700",
      ACP: "bg-purple-100 text-purple-700",
    },
    bC = {
      paid: "bg-green-100 text-green-700",
      fulfilled: "bg-teal-100 text-teal-700",
      cancelled: "bg-red-100 text-red-700",
      created: "bg-gray-100 text-gray-600",
      updated: "bg-gray-100 text-gray-600",
      refunded: "bg-yellow-100 text-yellow-700",
    },
    Xg = ({ label: e, classes: t }) =>
      i.jsx("span", {
        className: `rounded-full px-2 py-0.5 text-xs font-medium ${t}`,
        children: e,
      }),
    wC = ({ cols: e }) =>
      i.jsx("tr", {
        children: Array.from({ length: e }).map((t, r) =>
          i.jsx(
            "td",
            {
              className: "px-4 py-3",
              children: i.jsx("div", {
                className: "h-4 animate-pulse rounded bg-gray-100",
              }),
            },
            r
          )
        ),
      }),
    kC = ({ active: e, onChange: t, tabs: r, ariaLabel: n }) =>
      i.jsx("nav", {
        className: "flex border-b border-gray-200",
        role: "tablist",
        "aria-label": n,
        children: r.map((s) =>
          i.jsx(
            "button",
            {
              role: "tab",
              "aria-selected": e === s.id,
              onClick: () => t(s.id),
              className: `px-5 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 ${e === s.id ? "border-b-2 border-blue-600 text-blue-700" : "text-gray-500 hover:text-gray-700"}`,
              children: s.label,
            },
            s.id
          )
        ),
      }),
    _C = (e) => {
      const t = new URLSearchParams({ limit: "25" });
      return (
        e !== null && t.set("cursor", e),
        `/v1/embed/merchant/orders?${t.toString()}`
      );
    },
    SC = () => {
      const e = Re(),
        [t, r] = R.useState(null),
        [n, s] = R.useState([]),
        a = {
          SHOPIFY: e.misVentas.platformShopify,
          WOO: e.misVentas.platformWoo,
          ACP: e.misVentas.platformAcp,
        },
        o = {
          created: e.misVentas.statusCreated,
          updated: e.misVentas.statusUpdated,
          paid: e.misVentas.statusPaid,
          fulfilled: e.misVentas.statusFulfilled,
          cancelled: e.misVentas.statusCancelled,
          refunded: e.misVentas.statusRefunded,
        },
        l = _C(t),
        c = _e({
          queryKey: ["mis-ventas-orders", t],
          queryFn: () => ae.get(l, zh),
          placeholderData: (d) => d,
        });
      pe.useEffect(() => {
        c.data?.items &&
          s(
            t === null
              ? c.data.items
              : (d) => {
                  const f = new Set(d.map((m) => m.id));
                  return [
                    ...d,
                    ...(c.data?.items ?? []).filter((m) => !f.has(m.id)),
                  ];
                }
          );
      }, [c.data, t]);
      const u = n.length > 0 ? n : (c.data?.items ?? []);
      return i.jsxs("div", {
        className: "p-6",
        children: [
          i.jsx("p", {
            className: "mb-4 text-sm text-gray-500",
            children: e.misVentas.ordersSectionDesc,
          }),
          c.isError &&
            i.jsxs("div", {
              className:
                "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
              role: "alert",
              children: [
                e.misVentas.ordersErrorLoading,
                " ",
                c.error instanceof Error
                  ? c.error.message
                  : e.common.errorUnknown,
              ],
            }),
          !c.isError && u.length === 0 && !c.isLoading
            ? i.jsx("div", {
                className: "flex items-center gap-4",
                children: i.jsxs("div", {
                  children: [
                    i.jsx("p", {
                      className: "font-semibold text-gray-800",
                      children: e.misVentas.ordersEmpty,
                    }),
                    i.jsx("p", {
                      className: "text-sm text-gray-500",
                      children: e.misVentas.ordersEmptyDesc,
                    }),
                  ],
                }),
              })
            : i.jsx("div", {
                className: "overflow-x-auto",
                children: i.jsxs("table", {
                  className: "min-w-full text-sm",
                  "aria-label": e.misVentas.ordersTableLabel,
                  children: [
                    i.jsx("thead", {
                      children: i.jsxs("tr", {
                        className:
                          "border-b border-gray-200 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                        children: [
                          i.jsx("th", {
                            className: "px-4 py-3",
                            children: e.misVentas.colOrderNumber,
                          }),
                          i.jsx("th", {
                            className: "px-4 py-3",
                            children: e.misVentas.colStore,
                          }),
                          i.jsx("th", {
                            className: "px-4 py-3",
                            children: e.misVentas.colStatus,
                          }),
                          i.jsx("th", {
                            className: "px-4 py-3",
                            children: e.misVentas.colTotal,
                          }),
                          i.jsx("th", {
                            className: "px-4 py-3",
                            children: e.misVentas.colBuyer,
                          }),
                          i.jsx("th", {
                            className: "px-4 py-3",
                            children: e.misVentas.colDate,
                          }),
                        ],
                      }),
                    }),
                    i.jsxs("tbody", {
                      className: "divide-y divide-gray-100",
                      children: [
                        c.isLoading &&
                          Array.from({ length: 5 }).map((d, f) =>
                            i.jsx(wC, { cols: 6 }, f)
                          ),
                        u.map((d) =>
                          i.jsxs(
                            "tr",
                            {
                              className: "hover:bg-gray-50",
                              children: [
                                i.jsxs("td", {
                                  className:
                                    "px-4 py-3 font-mono text-xs text-gray-400",
                                  children: ["#", d.externalOrderId],
                                }),
                                i.jsx("td", {
                                  className: "px-4 py-3",
                                  children: i.jsx(Xg, {
                                    label: a[d.platform] ?? d.platform,
                                    classes:
                                      xC[d.platform] ??
                                      "bg-gray-100 text-gray-600",
                                  }),
                                }),
                                i.jsx("td", {
                                  className: "px-4 py-3",
                                  children: i.jsx(Xg, {
                                    label: o[d.status] ?? d.status,
                                    classes:
                                      bC[d.status] ??
                                      "bg-gray-100 text-gray-600",
                                  }),
                                }),
                                i.jsxs("td", {
                                  className:
                                    "px-4 py-3 font-semibold text-gray-800",
                                  children: [
                                    d.total.amount,
                                    " ",
                                    d.total.currency,
                                  ],
                                }),
                                i.jsx("td", {
                                  className: "px-4 py-3 text-xs text-gray-400",
                                  children: d.agentId
                                    ? e.misVentas.buyerAuto.replace(
                                        "{{id}}",
                                        d.agentId.slice(0, 8)
                                      )
                                    : e.misVentas.buyerHuman,
                                }),
                                i.jsx("td", {
                                  className: "px-4 py-3 text-gray-500",
                                  children: vC(d.createdAt),
                                }),
                              ],
                            },
                            d.id
                          )
                        ),
                      ],
                    }),
                  ],
                }),
              }),
          c.data?.nextCursor &&
            i.jsx("div", {
              className: "mt-4 text-center",
              children: i.jsx("button", {
                onClick: () => r(c.data?.nextCursor ?? null),
                disabled: c.isFetching,
                className:
                  "text-sm text-blue-600 hover:text-blue-800 disabled:opacity-50",
                children: c.isFetching
                  ? e.common.loadingMore
                  : e.misVentas.loadMoreOrders,
              }),
            }),
        ],
      });
    },
    CC = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              [t, r] = R.useState(Jg(window.location.hash)),
              [n, s] = R.useState(null),
              a = [
                { id: "orders", label: e.misVentas.tabOrders },
                { id: "receipts", label: e.misVentas.tabReceipts },
                { id: "keys", label: e.misVentas.tabKeys },
                { id: "audit", label: e.misVentas.tabAudit },
              ];
            R.useEffect(() => {
              const l = () => {
                r(Jg(window.location.hash));
              };
              return (
                window.addEventListener("hashchange", l),
                () => window.removeEventListener("hashchange", l)
              );
            }, []);
            const o = R.useCallback((l) => {
              r(l);
              const c = gC[l];
              (window.history.replaceState(
                null,
                "",
                c === "" ? window.location.pathname : c
              ),
                s(null));
            }, []);
            return i.jsxs("div", {
              className: "max-w-5xl",
              children: [
                i.jsx("div", {
                  className: "mb-0 px-6 pt-6",
                  children: i.jsx("h1", {
                    className: "text-xl font-semibold text-gray-900",
                    children: e.misVentas.title,
                  }),
                }),
                i.jsx(kC, {
                  active: t,
                  onChange: o,
                  tabs: a,
                  ariaLabel: e.misVentas.tabsAriaLabel,
                }),
                i.jsxs(R.Suspense, {
                  fallback: i.jsx("div", {
                    className: "p-6 text-sm text-gray-500",
                    children: e.trustCenter.loading,
                  }),
                  children: [
                    t === "orders" && i.jsx(SC, {}),
                    t === "receipts" && i.jsx(dC, { onSelect: s }),
                    t === "keys" && i.jsx(mC, {}),
                    t === "audit" && i.jsx(fC, {}),
                  ],
                }),
                n !== null &&
                  i.jsx(R.Suspense, {
                    fallback: null,
                    children: i.jsx(pC, { receipt: n, onClose: () => s(null) }),
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    jC = ({
      value: e,
      onChange: t,
      searchTaxonomy: r,
      disabled: n,
      debounceMs: s = 300,
      placeholder: a = "Search category…",
      removeLabel: o = "Remove {{name}}",
    }) => {
      const [l, c] = R.useState(""),
        [u, d] = R.useState([]),
        [f, m] = R.useState(!1),
        [v, x] = R.useState({}),
        b = R.useRef(null);
      R.useEffect(() => {
        b.current && clearTimeout(b.current);
        const p = l.trim();
        if (p === "") {
          d([]);
          return;
        }
        return (
          (b.current = setTimeout(() => {
            (m(!0),
              r(p)
                .then((y) => d(y))
                .catch(() => d([]))
                .finally(() => m(!1)));
          }, s)),
          () => {
            b.current && clearTimeout(b.current);
          }
        );
      }, [l, s, r]);
      const j = R.useCallback(
          (p) => {
            e.includes(p.id) ||
              (x((y) => ({ ...y, [p.id]: p.name })),
              t([...e, p.id]),
              c(""),
              d([]));
          },
          [e, t]
        ),
        g = R.useCallback((p) => t(e.filter((y) => y !== p)), [e, t]);
      return i.jsxs("div", {
        style: { display: "flex", flexDirection: "column", gap: 6 },
        children: [
          i.jsx("div", {
            style: { display: "flex", flexWrap: "wrap", gap: 6 },
            children: e.map((p) =>
              i.jsxs(
                "span",
                {
                  style: {
                    display: "inline-flex",
                    alignItems: "center",
                    gap: 4,
                    padding: "2px 8px",
                    background: "#e6f0ff",
                    border: "1px solid #b3d1ff",
                    borderRadius: 12,
                    fontSize: 12,
                  },
                  children: [
                    v[p] ?? p,
                    i.jsx("button", {
                      type: "button",
                      "aria-label": o.replace("{{name}}", v[p] ?? p),
                      disabled: n,
                      onClick: () => g(p),
                      style: {
                        border: "none",
                        background: "transparent",
                        cursor: "pointer",
                        fontSize: 14,
                        lineHeight: 1,
                      },
                      children: "×",
                    }),
                  ],
                },
                p
              )
            ),
          }),
          i.jsx("input", {
            type: "text",
            role: "combobox",
            "aria-expanded": u.length > 0,
            "aria-label": a,
            value: l,
            disabled: n,
            placeholder: a,
            onChange: (p) => c(p.target.value),
            style: { width: 320 },
          }),
          f
            ? i.jsx("small", { style: { color: "#666" }, children: "…" })
            : null,
          u.length > 0
            ? i.jsx("ul", {
                role: "listbox",
                style: {
                  listStyle: "none",
                  margin: 0,
                  padding: 4,
                  border: "1px solid #c3c4c7",
                  borderRadius: 4,
                  maxWidth: 320,
                  maxHeight: 180,
                  overflowY: "auto",
                },
                children: u.map((p) =>
                  i.jsx(
                    "li",
                    {
                      children: i.jsx("button", {
                        type: "button",
                        role: "option",
                        "aria-selected": e.includes(p.id),
                        disabled: n,
                        onClick: () => j(p),
                        style: {
                          display: "block",
                          width: "100%",
                          textAlign: "left",
                          border: "none",
                          background: "transparent",
                          padding: "4px 6px",
                          cursor: "pointer",
                        },
                        children: p.name,
                      }),
                    },
                    p.id
                  )
                ),
              })
            : null,
        ],
      });
    };
  function NC(e) {
    return e
      .split(",")
      .map((t) => t.trim())
      .filter((t) => t.length > 0);
  }
  function RC(e) {
    return Array.isArray(e) ? e.map((t) => String(t)).join(", ") : "";
  }
  const AC = ({
      ruleCode: e,
      value: t,
      onChange: r,
      disabled: n,
      error: s,
      meta: a,
      searchTaxonomy: o,
    }) => {
      const l = Re(),
        c = R.useCallback(
          (m, v) => {
            const x = { ...t };
            (v == null || v === "" ? delete x[m] : (x[m] = v), r(x));
          },
          [t, r]
        );
      if (!a || a.fields.length === 0) return null;
      const u = l.misReglas.params ?? {},
        d = (m) => u[m] ?? m,
        f = (m) => {
          const v = t[m.name],
            x = `amcp-param-${e}-${m.name}`;
          switch (m.type) {
            case "boolean":
              return i.jsxs(
                "label",
                {
                  htmlFor: x,
                  style: { display: "flex", alignItems: "center", gap: 8 },
                  children: [
                    i.jsx("input", {
                      id: x,
                      type: "checkbox",
                      checked: v === !0,
                      disabled: n,
                      onChange: (b) => c(m.name, b.target.checked || void 0),
                    }),
                    i.jsx("span", { children: d(m.labelKey) }),
                  ],
                },
                m.name
              );
            case "number":
              return i.jsxs(
                "label",
                {
                  htmlFor: x,
                  style: { display: "flex", flexDirection: "column", gap: 4 },
                  children: [
                    i.jsx("span", { children: d(m.labelKey) }),
                    i.jsx("input", {
                      id: x,
                      type: "number",
                      value: typeof v == "number" ? v : "",
                      disabled: n,
                      min: m.min,
                      max: m.max,
                      placeholder: m.placeholder,
                      onChange: (b) => {
                        const j = b.target.value;
                        if (j === "") return c(m.name, void 0);
                        const g = Number(j);
                        Number.isNaN(g) || c(m.name, g);
                      },
                      style: { width: 160 },
                    }),
                  ],
                },
                m.name
              );
            case "enum":
              return i.jsxs(
                "label",
                {
                  htmlFor: x,
                  style: { display: "flex", flexDirection: "column", gap: 4 },
                  children: [
                    i.jsx("span", { children: d(m.labelKey) }),
                    i.jsxs("select", {
                      id: x,
                      value: typeof v == "string" ? v : "",
                      disabled: n,
                      onChange: (b) => c(m.name, b.target.value || void 0),
                      style: { width: 220 },
                      children: [
                        i.jsx("option", { value: "", children: "—" }),
                        m.options?.map((b) =>
                          i.jsx("option", { value: b, children: b }, b)
                        ),
                      ],
                    }),
                  ],
                },
                m.name
              );
            case "taxonomy-select": {
              const b = Array.isArray(v) ? v : [];
              return i.jsxs(
                "div",
                {
                  style: { display: "flex", flexDirection: "column", gap: 4 },
                  children: [
                    i.jsx("span", { children: d(m.labelKey) }),
                    o
                      ? i.jsx(jC, {
                          value: b,
                          disabled: n,
                          searchTaxonomy: o,
                          placeholder: l.misReglas.taxonomySearchPlaceholder,
                          removeLabel: l.misReglas.taxonomyRemoveLabel,
                          onChange: (j) => c(m.name, j.length > 0 ? j : void 0),
                        })
                      : i.jsx("small", {
                          style: { color: "#b32d2e" },
                          children:
                            l.misReglas.taxonomyUnavailable ??
                            "Taxonomy search unavailable on this platform",
                        }),
                  ],
                },
                m.name
              );
            }
            case "multiselect": {
              const b = Array.isArray(v) ? v : [],
                j = (p, y) => {
                  const w = y
                    ? [...b.filter((S) => S !== p), p]
                    : b.filter((S) => S !== p);
                  c(m.name, w.length > 0 ? w : void 0);
                },
                g = (p) => u[`option.${p}`] ?? p;
              return i.jsxs(
                "div",
                {
                  role: "group",
                  "aria-label": d(m.labelKey),
                  style: { display: "flex", flexDirection: "column", gap: 4 },
                  children: [
                    i.jsx("span", { children: d(m.labelKey) }),
                    i.jsx("div", {
                      style: {
                        display: "flex",
                        flexWrap: "wrap",
                        gap: 8,
                        maxWidth: 480,
                      },
                      children: m.options?.map((p) => {
                        const y = `${x}-${p}`;
                        return i.jsxs(
                          "label",
                          {
                            htmlFor: y,
                            style: {
                              display: "flex",
                              alignItems: "center",
                              gap: 4,
                            },
                            children: [
                              i.jsx("input", {
                                id: y,
                                type: "checkbox",
                                checked: b.includes(p),
                                disabled: n,
                                onChange: (w) => j(p, w.target.checked),
                              }),
                              i.jsx("span", { children: g(p) }),
                            ],
                          },
                          p
                        );
                      }),
                    }),
                  ],
                },
                m.name
              );
            }
            case "string-array":
            case "country-array":
            case "payment-method-array":
              return i.jsxs(
                "label",
                {
                  htmlFor: x,
                  style: { display: "flex", flexDirection: "column", gap: 4 },
                  children: [
                    i.jsx("span", { children: d(m.labelKey) }),
                    i.jsx("input", {
                      id: x,
                      type: "text",
                      value: RC(v),
                      disabled: n,
                      placeholder: m.placeholder ?? "value1, value2",
                      onChange: (b) => {
                        const j = NC(b.target.value);
                        c(m.name, j.length > 0 ? j : void 0);
                      },
                      style: { width: 320 },
                    }),
                    i.jsx("small", {
                      style: { color: "#666" },
                      children:
                        l.misReglas.paramsCsvHint ?? "Comma-separated values",
                    }),
                  ],
                },
                m.name
              );
            default:
              return null;
          }
        };
      return i.jsxs("div", {
        style: {
          marginTop: 12,
          padding: 12,
          background: "#f6f7f7",
          border: "1px solid #c3c4c7",
          borderRadius: 4,
          display: "flex",
          flexDirection: "column",
          gap: 12,
        },
        children: [
          i.jsx("strong", {
            style: { fontSize: 13 },
            children: l.misReglas.paramsTitle ?? "Parameters",
          }),
          a.fields.map((m) => f(m)),
          a.requiredAnyOf
            ? i.jsx("small", {
                style: { color: "#646970" },
                children:
                  l.misReglas.paramsRequiredHint ??
                  `At least one of: ${a.requiredAnyOf.join(", ")}`,
              })
            : null,
          s
            ? i.jsx("div", {
                role: "alert",
                style: { color: "#b32d2e", fontSize: 12 },
                children: s,
              })
            : null,
        ],
      });
    },
    EC = oe()
      .trim()
      .length(2)
      .transform((e) => e.toUpperCase()),
    ey = oe()
      .trim()
      .min(1)
      .transform((e) => e.toLowerCase()),
    ty = W({}).strict(),
    ed = [
      "alcohol",
      "tobacco",
      "firearms",
      "pharmaceuticals",
      "adult",
      "gambling",
      "cbd",
      "electronics_high_value",
      "jewelry",
      "crypto",
      "gift_cards",
      "subscriptions",
    ],
    ry = [
      "card",
      "paypal",
      "crypto",
      "bnpl",
      "klarna",
      "afterpay",
      "bank_transfer",
      "apple_pay",
      "google_pay",
      "gift_card",
    ],
    td = {
      R001: {
        schema: W({
          requireAgentId: _t().optional(),
          merchantTier: Tn(["standard", "high"]).optional(),
        }).strict(),
        fields: [
          {
            name: "requireAgentId",
            type: "boolean",
            labelKey: "R001.requireAgentId",
          },
          {
            name: "merchantTier",
            type: "enum",
            labelKey: "R001.merchantTier",
            options: ["standard", "high"],
          },
        ],
      },
      R004: {
        schema: W({
          maxKeyAgeHours: ue().int().nonnegative().optional(),
        }).strict(),
        fields: [
          {
            name: "maxKeyAgeHours",
            type: "number",
            labelKey: "R004.maxKeyAgeHours",
            min: 0,
          },
        ],
        requiredAnyOf: ["maxKeyAgeHours"],
      },
      R005: { schema: ty, fields: [] },
      R007: { schema: ty, fields: [] },
      R009: {
        schema: W({ requireAgentId: _t().optional() }).strict(),
        fields: [
          {
            name: "requireAgentId",
            type: "boolean",
            labelKey: "R009.requireAgentId",
          },
        ],
      },
      R010: {
        schema: W({
          minCompletedOrders: ue().int().min(1).optional(),
        }).strict(),
        fields: [
          {
            name: "minCompletedOrders",
            type: "number",
            labelKey: "R010.minCompletedOrders",
            min: 1,
          },
        ],
        requiredAnyOf: ["minCompletedOrders"],
      },
      R012: {
        schema: W({ categories: He(Tn(ed)).optional() }).strict(),
        fields: [
          {
            name: "categories",
            type: "multiselect",
            labelKey: "R012.categories",
            options: ed,
          },
        ],
        requiredAnyOf: ["categories"],
      },
      R014: {
        schema: W({
          highRiskCountries: He(EC).optional(),
          maxCancellations: ue().int().nonnegative().optional(),
          windowDays: ue().int().positive().optional(),
        }).strict(),
        fields: [
          {
            name: "highRiskCountries",
            type: "country-array",
            labelKey: "R014.highRiskCountries",
            placeholder: "US, MX",
          },
          {
            name: "maxCancellations",
            type: "number",
            labelKey: "R014.maxCancellations",
            min: 0,
          },
          {
            name: "windowDays",
            type: "number",
            labelKey: "R014.windowDays",
            min: 1,
          },
        ],
        requiredAnyOf: ["highRiskCountries", "maxCancellations"],
      },
      R015: {
        schema: W({
          maxDeltaBps: ue().int().nonnegative().optional(),
        }).strict(),
        fields: [
          {
            name: "maxDeltaBps",
            type: "number",
            labelKey: "R015.maxDeltaBps",
            min: 0,
          },
        ],
        requiredAnyOf: ["maxDeltaBps"],
      },
      R016: {
        schema: W({ minStock: ue().int().nonnegative().optional() }).strict(),
        fields: [
          {
            name: "minStock",
            type: "number",
            labelKey: "R016.minStock",
            min: 0,
          },
        ],
        requiredAnyOf: ["minStock"],
      },
      R018: {
        schema: W({
          spikeMultiplier: ue().positive().optional(),
          merchantAvgOrderCents: ue().int().positive().optional(),
          maxItemCount: ue().int().positive().optional(),
          maxSingleSkuQty: ue().int().positive().optional(),
        }).strict(),
        fields: [
          {
            name: "spikeMultiplier",
            type: "number",
            labelKey: "R018.spikeMultiplier",
            min: 0,
          },
          {
            name: "merchantAvgOrderCents",
            type: "number",
            labelKey: "R018.merchantAvgOrderCents",
            min: 1,
          },
          {
            name: "maxItemCount",
            type: "number",
            labelKey: "R018.maxItemCount",
            min: 1,
          },
          {
            name: "maxSingleSkuQty",
            type: "number",
            labelKey: "R018.maxSingleSkuQty",
            min: 1,
          },
        ],
        requiredAnyOf: [
          "merchantAvgOrderCents",
          "maxItemCount",
          "maxSingleSkuQty",
        ],
      },
      R021: {
        schema: W({
          minCompletedOrders: ue().int().nonnegative().optional(),
        }).strict(),
        fields: [
          {
            name: "minCompletedOrders",
            type: "number",
            labelKey: "R021.minCompletedOrders",
            min: 0,
          },
        ],
        requiredAnyOf: ["minCompletedOrders"],
      },
      R022: {
        schema: W({
          allowedPaymentMethods: He(ey).optional(),
          blockedPaymentMethods: He(ey).optional(),
        }).strict(),
        fields: [
          {
            name: "allowedPaymentMethods",
            type: "multiselect",
            labelKey: "R022.allowedPaymentMethods",
            options: ry,
          },
          {
            name: "blockedPaymentMethods",
            type: "multiselect",
            labelKey: "R022.blockedPaymentMethods",
            options: ry,
          },
        ],
        requiredAnyOf: ["allowedPaymentMethods", "blockedPaymentMethods"],
      },
      R023: {
        schema: W({
          windowDays: ue().int().positive().optional(),
          maxRatio: ue().min(0).max(1).optional(),
        }).strict(),
        fields: [
          {
            name: "windowDays",
            type: "number",
            labelKey: "R023.windowDays",
            min: 1,
          },
          {
            name: "maxRatio",
            type: "number",
            labelKey: "R023.maxRatio",
            min: 0,
            max: 1,
          },
        ],
        requiredAnyOf: ["maxRatio"],
      },
      R024: {
        schema: W({
          windowDays: ue().int().positive().optional(),
          maxDisputes: ue().int().nonnegative().optional(),
        }).strict(),
        fields: [
          {
            name: "windowDays",
            type: "number",
            labelKey: "R024.windowDays",
            min: 1,
          },
          {
            name: "maxDisputes",
            type: "number",
            labelKey: "R024.maxDisputes",
            min: 0,
          },
        ],
        requiredAnyOf: ["maxDisputes"],
      },
      R025: {
        schema: W({
          blockPoBox: _t().optional(),
          blockFreightForwarder: _t().optional(),
        }).strict(),
        fields: [
          { name: "blockPoBox", type: "boolean", labelKey: "R025.blockPoBox" },
          {
            name: "blockFreightForwarder",
            type: "boolean",
            labelKey: "R025.blockFreightForwarder",
          },
        ],
      },
      R026: {
        schema: W({ requireConsent: _t().optional() }).strict(),
        fields: [
          {
            name: "requireConsent",
            type: "boolean",
            labelKey: "R026.requireConsent",
          },
        ],
      },
      R027: {
        schema: W({
          maxStoredValueCents: ue().int().nonnegative().optional(),
        }).strict(),
        fields: [
          {
            name: "maxStoredValueCents",
            type: "number",
            labelKey: "R027.maxStoredValueCents",
            min: 0,
          },
        ],
        requiredAnyOf: ["maxStoredValueCents"],
      },
      R028: {
        schema: W({ requirePurchaseOrder: _t().optional() }).strict(),
        fields: [
          {
            name: "requirePurchaseOrder",
            type: "boolean",
            labelKey: "R028.requirePurchaseOrder",
          },
        ],
      },
      R032: {
        schema: W({ blockedCategoryIds: He(oe()).optional() }).strict(),
        fields: [
          {
            name: "blockedCategoryIds",
            type: "multiselect",
            labelKey: "R032.blockedCategoryIds",
            options: ed,
          },
        ],
        requiredAnyOf: ["blockedCategoryIds"],
      },
      R034: {
        schema: W({ blockedSkus: He(oe()).optional() }).strict(),
        fields: [
          {
            name: "blockedSkus",
            type: "string-array",
            labelKey: "R034.blockedSkus",
            placeholder: "SKU-123, SKU-456",
          },
        ],
        requiredAnyOf: ["blockedSkus"],
      },
      R035: {
        schema: W({ maxCents: ue().int().positive().optional() }).strict(),
        fields: [
          {
            name: "maxCents",
            type: "number",
            labelKey: "R035.maxCents",
            min: 1,
          },
        ],
        requiredAnyOf: ["maxCents"],
      },
      R036: {
        schema: W({
          maxCentsPerLine: ue().int().positive().optional(),
        }).strict(),
        fields: [
          {
            name: "maxCentsPerLine",
            type: "number",
            labelKey: "R036.maxCentsPerLine",
            min: 1,
          },
        ],
        requiredAnyOf: ["maxCentsPerLine"],
      },
      R038: {
        schema: W({ maxQuantity: ue().int().positive().optional() }).strict(),
        fields: [
          {
            name: "maxQuantity",
            type: "number",
            labelKey: "R038.maxQuantity",
            min: 1,
          },
        ],
        requiredAnyOf: ["maxQuantity"],
      },
      R039: {
        schema: W({ maxPerSku: ue().int().positive().optional() }).strict(),
        fields: [
          {
            name: "maxPerSku",
            type: "number",
            labelKey: "R039.maxPerSku",
            min: 1,
          },
        ],
        requiredAnyOf: ["maxPerSku"],
      },
      R043: {
        schema: W({ ttlMinutes: ue().int().positive().optional() }).strict(),
        fields: [
          {
            name: "ttlMinutes",
            type: "number",
            labelKey: "R043.ttlMinutes",
            min: 1,
          },
        ],
      },
      R048: {
        schema: W({
          blockedTypes: He(
            Tn(["gift_card", "license_key", "downloadable", "stored_value"])
          ).optional(),
        }).strict(),
        fields: [
          {
            name: "blockedTypes",
            type: "multiselect",
            labelKey: "R048.blockedTypes",
            options: [
              "gift_card",
              "license_key",
              "downloadable",
              "stored_value",
            ],
          },
        ],
      },
    },
    TC = W({
      name: oe(),
      type: Tn([
        "boolean",
        "number",
        "enum",
        "string-array",
        "country-array",
        "payment-method-array",
        "multiselect",
        "taxonomy-select",
      ]),
      labelKey: oe(),
      helpKey: oe().optional(),
      min: ue().optional(),
      max: ue().optional(),
      options: He(oe()).optional(),
      placeholder: oe().optional(),
    }),
    PC = W({
      ruleCode: oe(),
      prefix: oe(),
      fields: He(TC),
      requiredAnyOf: He(oe()).optional(),
      platformAvailability: Qu(W({ status: oe(), reason: oe().optional() }))
        .nullable()
        .optional(),
    }),
    OC = W({ rules: He(PC), version: ue() }),
    IC = /^(R\d{3})/;
  function LC(e) {
    const t = IC.exec(e.trim().toUpperCase());
    return t ? t[1] : null;
  }
  const DC = {
    "wp-embed": "woocommerce",
    "ps-embed": "prestashop",
    "odoo-embed": "odoo",
    "magento-embed": "magento",
    "shopify-embed": "shopify",
  };
  function zC() {
    return {
      ready: !0,
      source: "fallback",
      ruleCodes: Object.keys(td),
      metaByPrefix: td,
    };
  }
  function MC(e) {
    const t = {};
    for (const r of e)
      t[r.prefix] = {
        schema: td[r.prefix]?.schema ?? void 0,
        fields: r.fields.map((n) => ({
          name: n.name,
          type: n.type,
          labelKey: n.labelKey,
          helpKey: n.helpKey,
          min: n.min,
          max: n.max,
          options: n.options,
          placeholder: n.placeholder,
        })),
        requiredAnyOf: r.requiredAnyOf,
      };
    return {
      ready: !0,
      source: "live",
      ruleCodes: e.map((r) => r.prefix),
      metaByPrefix: t,
    };
  }
  function $C() {
    const e = Wh(),
      t = DC[e] ?? "woocommerce",
      r = _e({
        queryKey: ["rule-params-meta", t],
        queryFn: () =>
          ae.get(
            `/v1/public/rule-params-meta?platform=${encodeURIComponent(t)}`,
            OC
          ),
        staleTime: 3600 * 1e3,
        gcTime: 1440 * 60 * 1e3,
        retry: 1,
      });
    return r.data && Array.isArray(r.data.rules) && r.data.rules.length > 0
      ? MC(r.data.rules)
      : zC();
  }
  function ny(e, t) {
    const r = LC(t);
    return r ? (e.metaByPrefix[r] ?? null) : null;
  }
  function sy(e, t, r) {
    const n = ny(e, t);
    return n?.requiredAnyOf
      ? n.requiredAnyOf.some((a) => {
          const o = r?.[a];
          return o == null
            ? !1
            : Array.isArray(o)
              ? o.length > 0
              : typeof o == "string"
                ? o.trim().length > 0
                : !0;
        })
        ? { ok: !0 }
        : { ok: !1, requiredAnyOf: n.requiredAnyOf }
      : { ok: !0 };
  }
  const FC = {
      identity: "🪪",
      behavior: "👁️",
      transaction: "💳",
      postsale: "📦",
      general: "⚙️",
    },
    UC = W({ results: He(W({ id: oe(), name: oe() })) });
  async function ZC(e) {
    return (
      await ae.get(
        `/v1/embed/merchant/taxonomy/search?q=${encodeURIComponent(e)}`,
        UC
      )
    ).results;
  }
  const BC = W({
      id: oe().optional(),
      storeId: oe().optional(),
      ruleCode: oe(),
      enabled: _t(),
      params: Qu(Ba()).optional().nullable(),
      mode: oe().optional(),
    }),
    qC = W({ rules: He(BC), activeCount: ue() }),
    VC = W({
      rule: W({
        ruleCode: oe(),
        enabled: _t(),
        params: Qu(Ba()).optional().nullable(),
      }),
    }),
    KC = [
      "R001.verified-agent-required",
      "R004.new-key-friction",
      "R005.revoked-agent-block",
      "R007.cross-merchant-abuse-signal",
      "R009.agent-verification-required",
      "R010.new-agent-probation",
      "R012.high-risk-category",
      "R014.delivery-risk-guard",
      "R015.price-change-guard",
      "R016.stock-confidence-guard",
      "R018.cart-composition-guard",
      "R021.first-purchase-with-merchant",
      "R022.payment-rail-restriction",
      "R023.refund-abuse-guard",
      "R024.dispute-history-guard",
      "R025.sensitive-delivery-address",
      "R026.subscription-autorenew-guard",
      "R027.gift-card-stored-value",
      "R028.b2b-po-guard",
      "R035.max-order-value",
      "R036.max-line-item-value",
      "R038.max-items-per-order",
      "R039.max-quantity-per-sku",
      "R034.sku-blocklist",
      "R032.category-blocklist",
      "R048.no-digital-goods-for-agents",
      "R043.agent-checkout-approval-required",
    ].map((e) => ({ ruleCode: e, enabled: !1 })),
    WC = ({
      rule: e,
      stat: t,
      onToggle: r,
      onSaveParams: n,
      onParamsRequired: s,
      isPending: a,
      t: o,
      metaState: l,
    }) => {
      const c = ny(l, e.ruleCode),
        u = e.params ?? {},
        [d, f] = R.useState(u),
        [m, v] = R.useState(null),
        x = R.useRef(null),
        b = JSON.stringify(u);
      pe.useEffect(() => {
        (f(u), v(null));
      }, [b, e.ruleCode]);
      const j = JSON.stringify(d) !== b,
        g = e.ruleCode.match(/^(R\d{3})/)?.[1] ?? null,
        y = o.misReglas.rulesMeta[e.ruleCode],
        w = y
          ? { ...y, helpUrl: g ? `${o.misReglas.helpBase}#${g}` : "" }
          : {
              title: e.ruleCode,
              description: "",
              helpUrl: g ? `${o.misReglas.helpBase}#${g}` : "",
            },
        S = e.optimisticEnabled ?? e.enabled,
        A = t?.blockedCount ?? 0,
        N = t?.failOpenCount ?? 0;
      return i.jsxs("div", {
        className: "flex items-start gap-4 border-b border-gray-100 py-4",
        style: { opacity: a ? 0.6 : 1 },
        children: [
          i.jsx("button", {
            type: "button",
            role: "switch",
            "aria-checked": S,
            disabled: a,
            onClick: () => {
              const T = !S;
              if (T) {
                const _ = sy(l, e.ruleCode, d);
                if (!_.ok) {
                  const O =
                    (o.misReglas.paramsRequiredError ??
                      "Required parameter missing:") +
                    " " +
                    (_.requiredAnyOf ?? []).join(" / ");
                  (v(O),
                    s(w.title, _.requiredAnyOf ?? []),
                    x.current?.scrollIntoView({
                      behavior: "smooth",
                      block: "nearest",
                    }));
                  return;
                }
              }
              (v(null), r(e.ruleCode, T, d));
            },
            className:
              "relative mt-0.5 h-6 w-11 shrink-0 rounded-full border-none transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed",
            style: { background: S ? "#1a7f4f" : "#d1d5db" },
            "aria-label": S
              ? o.misReglas.toggleAriaActive
              : o.misReglas.toggleAriaInactive,
            children: i.jsx("span", {
              className:
                "absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition-all",
              style: { left: S ? "22px" : "2px" },
            }),
          }),
          i.jsxs("div", {
            className: "flex-1",
            children: [
              i.jsxs("div", {
                className: "mb-1 flex flex-wrap items-center gap-2",
                children: [
                  i.jsx("span", {
                    className: "text-sm font-semibold text-gray-900",
                    children: w.title,
                  }),
                  S
                    ? i.jsx("span", {
                        className:
                          "rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700",
                        children: o.misReglas.ruleActive,
                      })
                    : i.jsx("span", {
                        className:
                          "rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500",
                        children: o.misReglas.ruleInactive,
                      }),
                  A > 0 &&
                    i.jsx("span", {
                      className:
                        "rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700",
                      children: o.misReglas.blockedLast7d.replace(
                        "{{count}}",
                        String(A)
                      ),
                    }),
                  N > 0 &&
                    A === 0 &&
                    i.jsx("span", {
                      className:
                        "rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700",
                      children: o.misReglas.failOpenLast7d.replace(
                        "{{count}}",
                        String(N)
                      ),
                    }),
                ],
              }),
              i.jsx("p", {
                className: "text-sm text-gray-500",
                children: w.description,
              }),
              w.helpUrl &&
                i.jsx("a", {
                  href: w.helpUrl,
                  target: "_blank",
                  rel: "noopener noreferrer",
                  className:
                    "mt-1 inline-block text-xs text-blue-600 hover:text-blue-800 underline",
                  children: o.misReglas.helpLinkLabel,
                }),
              c && c.fields.length > 0
                ? i.jsxs(i.Fragment, {
                    children: [
                      i.jsx("div", {
                        ref: x,
                        children: i.jsx(AC, {
                          ruleCode: e.ruleCode,
                          value: d,
                          onChange: (T) => {
                            (f(T), v(null));
                          },
                          disabled: a,
                          error: m,
                          meta: c,
                          searchTaxonomy: ZC,
                        }),
                      }),
                      j
                        ? i.jsxs("div", {
                            style: { marginTop: 8 },
                            children: [
                              i.jsx("button", {
                                type: "button",
                                className: "button button-primary",
                                disabled: a,
                                onClick: () => {
                                  if (S) {
                                    const T = sy(l, e.ruleCode, d);
                                    if (!T.ok) {
                                      v(
                                        (o.misReglas.paramsRequiredError ??
                                          "Required parameter missing:") +
                                          " " +
                                          (T.requiredAnyOf ?? []).join(" / ")
                                      );
                                      return;
                                    }
                                  }
                                  (v(null), n(e.ruleCode, d, S));
                                },
                                children:
                                  o.misReglas.paramsSaveBtn ??
                                  "Save parameters",
                              }),
                              i.jsx("button", {
                                type: "button",
                                className: "button",
                                style: { marginLeft: 8 },
                                disabled: a,
                                onClick: () => {
                                  (f(u), v(null));
                                },
                                children: o.misReglas.paramsResetBtn ?? "Reset",
                              }),
                            ],
                          })
                        : null,
                    ],
                  })
                : null,
            ],
          }),
        ],
      });
    },
    HC = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              t = fr(),
              [r, n] = R.useState([]),
              s = R.useRef(0),
              [a, o] = R.useState(new Set()),
              [l, c] = R.useState({}),
              u = $C(),
              d = R.useMemo(() => new Set(u.ruleCodes), [u.ruleCodes]),
              f = R.useCallback((_, O) => {
                const q = ++s.current;
                (n((ee) => [...ee, { id: q, message: _, type: O }]),
                  setTimeout(
                    () => n((ee) => ee.filter((Z) => Z.id !== q)),
                    5e3
                  ));
              }, []),
              m = _e({
                queryKey: ["merchant-rules"],
                queryFn: () => ae.get("/v1/embed/merchant/rules", qC),
              }),
              v = _e({
                queryKey: ["merchant-stats-7d"],
                queryFn: () =>
                  ae.get(
                    "/v1/embed/merchant/stats?window=7",
                    W({ stats: He(Fg()) }).optional()
                  ),
              }),
              x = Pt({
                mutationFn: ({ ruleCode: _, enabled: O, params: q }) =>
                  ae.put(
                    `/v1/embed/merchant/rules/${encodeURIComponent(_)}`,
                    q !== void 0 ? { enabled: O, params: q } : { enabled: O },
                    VC
                  ),
                onMutate: ({ ruleCode: _, enabled: O }) => {
                  (o((q) => new Set([...q, _])), c((q) => ({ ...q, [_]: O })));
                },
                onSuccess: (_, { ruleCode: O }) => {
                  (f(
                    _.rule.enabled
                      ? e.misReglas.ruleActivatedMsg
                      : e.misReglas.ruleDeactivatedMsg,
                    "success"
                  ),
                    c((q) => ({ ...q, [O]: _.rule.enabled })),
                    t.invalidateQueries({ queryKey: ["merchant-rules"] }));
                },
                onError: (_, { ruleCode: O }) => {
                  (f(e.misReglas.ruleChangeError, "error"),
                    c((q) => {
                      const ee = { ...q };
                      return (delete ee[O], ee);
                    }));
                },
                onSettled: (_, O, { ruleCode: q }) => {
                  o((ee) => {
                    const Z = new Set(ee);
                    return (Z.delete(q), Z);
                  });
                },
              }),
              j = (m.data?.rules ?? (m.isError ? KC : [])).filter((_) => {
                const O = _.ruleCode.match(/^(R\d{3})/);
                return O ? d.has(O[1]) : !1;
              }),
              g = j.filter((_) =>
                l[_.ruleCode] !== void 0 ? l[_.ruleCode] : _.enabled
              ).length,
              p = (_) => {
                const O = j.find((q) => q.ruleCode === _);
                return O ? (l[_] !== void 0 ? l[_] : O.enabled) : !1;
              },
              y =
                p("R001.verified-agent-required") &&
                p("R009.agent-verification-required"),
              w =
                !p("R001.verified-agent-required") &&
                p("R002.signature-spoof-block"),
              S = v.data?.stats ?? [],
              A = Object.fromEntries(S.map((_) => [_.ruleCode, _])),
              N = [
                "identity",
                "behavior",
                "transaction",
                "postsale",
                "general",
              ],
              T = {};
            for (const _ of N) T[_] = [];
            for (const _ of j) {
              const O = e.misReglas.rulesMeta[_.ruleCode]?.category ?? "other";
              (T[O] || (T[O] = []), T[O].push(_));
            }
            return i.jsxs("div", {
              className: "max-w-3xl space-y-6 p-6",
              children: [
                i.jsx("h1", {
                  className: "text-xl font-semibold text-gray-900",
                  children: e.misReglas.title,
                }),
                i.jsx("div", {
                  className: "fixed right-4 top-4 z-50 space-y-2",
                  children: r.map((_) =>
                    i.jsx(
                      "div",
                      {
                        className: `rounded-lg px-4 py-3 text-sm font-medium shadow-lg ${_.type === "success" ? "bg-green-600 text-white" : "bg-red-600 text-white"}`,
                        children: _.message,
                      },
                      _.id
                    )
                  ),
                }),
                i.jsx("div", {
                  className:
                    "rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800",
                  children: e.misReglas.infoNotice,
                }),
                m.isError &&
                  i.jsx("div", {
                    className:
                      "rounded border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800",
                    children: e.misReglas.errorNotice,
                  }),
                y &&
                  i.jsx("div", {
                    role: "alert",
                    className:
                      "rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800",
                    children: e.misReglas.r001r009Redundancy,
                  }),
                w &&
                  i.jsx("div", {
                    role: "alert",
                    className:
                      "rounded border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800",
                    children: e.misReglas.r002WithoutR001,
                  }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsxs("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: [
                        i.jsx("h2", {
                          className: "font-semibold text-gray-900",
                          children: e.misReglas.rulesByCategoryTitle,
                        }),
                        i.jsx("div", {
                          className: "mt-1",
                          children: i.jsx("span", {
                            className: `rounded-full px-2.5 py-0.5 text-xs font-medium ${g > 0 ? "bg-green-100 text-green-700" : "bg-gray-100 text-gray-500"}`,
                            children: e.misReglas.activeCountLabel
                              .replace("{{active}}", String(g))
                              .replace("{{total}}", String(j.length)),
                          }),
                        }),
                      ],
                    }),
                    i.jsx("div", {
                      className: "px-6 py-4",
                      children: m.isLoading
                        ? Array.from({ length: 4 }).map((_, O) =>
                            i.jsx(
                              "div",
                              {
                                className:
                                  "mb-4 h-16 animate-pulse rounded-lg bg-gray-100",
                              },
                              O
                            )
                          )
                        : N.map((_) => {
                            const O = T[_] ?? [];
                            if (O.length === 0) return null;
                            const q = e.misReglas.categoryLabels[_] ?? _;
                            return i.jsxs(
                              "div",
                              {
                                className: "mb-4",
                                children: [
                                  i.jsxs("div", {
                                    className:
                                      "flex items-center gap-1.5 border-b-2 border-gray-100 pb-1 pt-2 text-xs font-bold uppercase tracking-wider text-gray-400",
                                    children: [
                                      i.jsx("span", { children: FC[_] }),
                                      q,
                                    ],
                                  }),
                                  O.map((ee) =>
                                    i.jsx(
                                      WC,
                                      {
                                        rule: {
                                          ...ee,
                                          optimisticEnabled: l[ee.ruleCode],
                                        },
                                        metaState: u,
                                        stat: A[ee.ruleCode] ?? null,
                                        onToggle: (Z, Oe, nr) =>
                                          x.mutate({
                                            ruleCode: Z,
                                            enabled: Oe,
                                            params: nr,
                                          }),
                                        onSaveParams: (Z, Oe, nr) =>
                                          x.mutate({
                                            ruleCode: Z,
                                            enabled: nr,
                                            params: Oe,
                                          }),
                                        onParamsRequired: (Z, Oe) => {
                                          const nr =
                                            e.misReglas.paramsRequiredError ??
                                            "Required parameter missing:";
                                          f(
                                            `${nr} ${Oe.join(" / ")} — ${Z}`,
                                            "error"
                                          );
                                        },
                                        isPending: a.has(ee.ruleCode),
                                        t: e,
                                      },
                                      ee.ruleCode
                                    )
                                  ),
                                ],
                              },
                              _
                            );
                          }),
                    }),
                  ],
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: i.jsx("h2", {
                        className: "font-semibold text-gray-900",
                        children: e.misReglas.howItWorksTitle,
                      }),
                    }),
                    i.jsxs("div", {
                      className: "space-y-3 p-6 text-sm",
                      children: [
                        i.jsxs("div", {
                          className: "flex gap-3",
                          children: [
                            i.jsx("span", {
                              className: "text-lg",
                              children: "🟢",
                            }),
                            i.jsxs("p", {
                              className: "text-gray-600",
                              children: [
                                i.jsx("strong", {
                                  className: "text-gray-900",
                                  children: e.misReglas.howInactiveLabel,
                                }),
                                " ",
                                e.misReglas.howInactive,
                              ],
                            }),
                          ],
                        }),
                        i.jsxs("div", {
                          className: "flex gap-3",
                          children: [
                            i.jsx("span", {
                              className: "text-lg",
                              children: "🔴",
                            }),
                            i.jsxs("p", {
                              className: "text-gray-600",
                              children: [
                                i.jsx("strong", {
                                  className: "text-gray-900",
                                  children: e.misReglas.howActiveLabel,
                                }),
                                " ",
                                e.misReglas.howActive,
                              ],
                            }),
                          ],
                        }),
                        i.jsxs("div", {
                          className: "flex gap-3",
                          children: [
                            i.jsx("span", {
                              className: "text-lg",
                              children: "📊",
                            }),
                            i.jsxs("p", {
                              className: "text-gray-600",
                              children: [
                                i.jsx("strong", {
                                  className: "text-gray-900",
                                  children: e.misReglas.howStatsLabel,
                                }),
                                " ",
                                e.misReglas.howStats,
                              ],
                            }),
                          ],
                        }),
                      ],
                    }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    QC = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    GC = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "long",
      timeZone: "UTC",
    }),
    YC = (e) => {
      try {
        return QC.format(new Date(e));
      } catch {
        return e;
      }
    },
    JC = (e) => {
      if (!e) return "—";
      try {
        return GC.format(new Date(e));
      } catch {
        return e;
      }
    },
    XC = {
      "shopify-embed": "bg-green-50 text-green-700",
      "wp-embed": "bg-blue-50 text-blue-700",
      portal: "bg-gray-100 text-gray-700",
      api: "bg-yellow-50 text-yellow-700",
      mcp: "bg-purple-50 text-purple-700",
      webhook: "bg-orange-50 text-orange-700",
    },
    ej = () =>
      i.jsx("tr", {
        children: Array.from({ length: 5 }).map((e, t) =>
          i.jsx(
            "td",
            {
              className: "px-4 py-3",
              children: i.jsx("div", {
                className: "h-4 animate-pulse rounded bg-gray-100",
              }),
            },
            t
          )
        ),
      }),
    tj = (e) => {
      const t = new URLSearchParams({ limit: "25" });
      return (
        e !== null && t.set("cursor", e),
        `/v1/embed/trust/audit?${t.toString()}`
      );
    },
    rj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              [t, r] = R.useState(null),
              [n, s] = R.useState([]),
              a = {
                "shopify-embed": e.seguridad.sourceShopify,
                "wp-embed": e.seguridad.sourceWp,
                portal: e.seguridad.sourcePortal,
                api: e.seguridad.sourceApi,
                mcp: e.seguridad.sourceMcp,
                webhook: e.seguridad.sourceWebhook,
              },
              o = (x) =>
                x === "allow"
                  ? e.seguridad.outcomeAllowed
                  : x === "deny" || x === "blocked_by_overlay"
                    ? e.seguridad.outcomeBlocked
                    : x === "token_refresh_seamless"
                      ? e.seguridad.outcomeSessionRenewed
                      : x,
              l = _e({
                queryKey: ["trust-keys-seguridad"],
                queryFn: async () => {
                  try {
                    return await ae.get("/v1/embed/trust/keys", Fa);
                  } catch (x) {
                    if (x instanceof Bt && x.status === 404) return null;
                    throw x;
                  }
                },
                retry: !1,
              }),
              c = tj(t),
              u = _e({
                queryKey: ["trust-audit-seguridad", t],
                queryFn: () => ae.get(c, Ph),
                placeholderData: (x) => x,
              });
            pe.useEffect(() => {
              u.data?.items &&
                s(
                  t === null
                    ? u.data.items
                    : (x) => {
                        const b = new Set(x.map((j) => j.id));
                        return [
                          ...x,
                          ...(u.data?.items ?? []).filter((j) => !b.has(j.id)),
                        ];
                      }
                );
            }, [u.data, t]);
            const d = n.length > 0 ? n : (u.data?.items ?? []),
              f = u.data?.chainIntegrity ?? null,
              m = l.data,
              v = !l.isLoading && !l.isError && m === null;
            return i.jsxs("div", {
              className: "max-w-5xl space-y-6 p-6",
              children: [
                i.jsx("h1", {
                  className: "text-xl font-semibold text-gray-900",
                  children: e.seguridad.title,
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: i.jsx("h2", {
                        className: "font-semibold text-gray-900",
                        children: e.seguridad.protectionSection,
                      }),
                    }),
                    i.jsxs("div", {
                      className: "p-6",
                      children: [
                        l.isError &&
                          i.jsxs("div", {
                            className:
                              "rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                            role: "alert",
                            children: [
                              e.seguridad.errorChecking,
                              " ",
                              l.error instanceof Error
                                ? l.error.message
                                : e.common.errorUnknown,
                            ],
                          }),
                        l.isLoading &&
                          i.jsx("div", {
                            className:
                              "h-16 animate-pulse rounded-lg bg-gray-100",
                          }),
                        v &&
                          i.jsxs("div", {
                            className:
                              "rounded-lg border border-blue-100 bg-blue-50 px-4 py-3",
                            role: "status",
                            children: [
                              i.jsx("p", {
                                className:
                                  "text-sm font-semibold text-blue-900",
                                children: e.seguridad.notActivatedTitle,
                              }),
                              i.jsx("p", {
                                className: "mt-1 text-sm text-blue-800",
                                children: e.seguridad.notActivatedDesc,
                              }),
                            ],
                          }),
                        m &&
                          i.jsxs("div", {
                            className: "space-y-3",
                            children: [
                              i.jsxs("div", {
                                className: "flex items-center gap-4",
                                children: [
                                  i.jsxs("svg", {
                                    width: "52",
                                    height: "60",
                                    viewBox: "0 0 52 60",
                                    fill: "none",
                                    "aria-hidden": "true",
                                    children: [
                                      i.jsx("path", {
                                        d: "M26 2L4 11v16c0 14.4 9.4 27.8 22 31.6C38.6 54.8 48 41.4 48 27V11L26 2z",
                                        fill: "#e3f5ec",
                                        stroke: "#1a7f4f",
                                        strokeWidth: "2.5",
                                        strokeLinejoin: "round",
                                      }),
                                      i.jsx("path", {
                                        d: "M16 30l7 7 13-14",
                                        stroke: "#1a7f4f",
                                        strokeWidth: "3",
                                        strokeLinecap: "round",
                                        strokeLinejoin: "round",
                                      }),
                                    ],
                                  }),
                                  i.jsxs("div", {
                                    children: [
                                      i.jsx("span", {
                                        className:
                                          "inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 mb-1",
                                        children: e.seguridad.protectionBadge,
                                      }),
                                      i.jsx("p", {
                                        className: "text-sm text-gray-500",
                                        children: e.seguridad.protectionDesc,
                                      }),
                                      m.daysSinceRotation !== null &&
                                        i.jsxs("p", {
                                          className:
                                            "mt-1 text-xs text-gray-400",
                                          children: [
                                            e.seguridad.lastRenewal.replace(
                                              "{{days}}",
                                              String(m.daysSinceRotation)
                                            ),
                                            m.lastRotatedAt &&
                                              ` (${JC(m.lastRotatedAt)})`,
                                          ],
                                        }),
                                    ],
                                  }),
                                ],
                              }),
                              m.retiredGraceKids.length > 0 &&
                                i.jsx("div", {
                                  className:
                                    "rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800",
                                  children: e.seguridad.graceKeysNotice.replace(
                                    "{{count}}",
                                    String(m.retiredGraceKids.length)
                                  ),
                                }),
                            ],
                          }),
                      ],
                    }),
                  ],
                }),
                i.jsxs("section", {
                  className:
                    "rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: [
                    i.jsxs("div", {
                      className: "border-b border-gray-100 px-6 py-4",
                      children: [
                        i.jsx("h2", {
                          className: "font-semibold text-gray-900",
                          children: e.seguridad.activitySection,
                        }),
                        i.jsx("p", {
                          className: "mt-1 text-sm text-gray-500",
                          children: e.seguridad.activitySectionDesc,
                        }),
                      ],
                    }),
                    i.jsxs("div", {
                      className: "p-6",
                      children: [
                        f &&
                          i.jsxs("div", {
                            className: `mb-4 rounded border px-4 py-3 text-sm ${f.verified ? "border-green-300 bg-green-50 text-green-800" : "border-yellow-300 bg-yellow-50 text-yellow-800"}`,
                            children: [
                              f.verified
                                ? e.seguridad.chainOk
                                : e.seguridad.chainBroken,
                              f.note && ` ${f.note}`,
                            ],
                          }),
                        u.isError &&
                          i.jsxs("div", {
                            className:
                              "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                            role: "alert",
                            children: [
                              e.seguridad.errorLoadingActivity,
                              " ",
                              u.error instanceof Error
                                ? u.error.message
                                : e.common.errorUnknown,
                            ],
                          }),
                        !u.isError && d.length === 0 && !u.isLoading
                          ? i.jsx("div", {
                              className: "flex items-center gap-4",
                              children: i.jsxs("div", {
                                children: [
                                  i.jsx("p", {
                                    className: "font-semibold text-gray-800",
                                    children: e.seguridad.activityEmpty,
                                  }),
                                  i.jsx("p", {
                                    className: "text-sm text-gray-500",
                                    children: e.seguridad.activityEmptyDesc,
                                  }),
                                ],
                              }),
                            })
                          : i.jsx("div", {
                              className: "overflow-x-auto",
                              children: i.jsxs("table", {
                                className: "min-w-full text-sm",
                                "aria-label": e.seguridad.tableLabel,
                                children: [
                                  i.jsx("thead", {
                                    children: i.jsxs("tr", {
                                      className:
                                        "border-b border-gray-200 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                                      children: [
                                        i.jsx("th", {
                                          className: "px-4 py-3",
                                          children: e.seguridad.colWhen,
                                        }),
                                        i.jsx("th", {
                                          className: "px-4 py-3",
                                          children: e.seguridad.colFrom,
                                        }),
                                        i.jsx("th", {
                                          className: "px-4 py-3",
                                          children: e.seguridad.colResult,
                                        }),
                                        i.jsx("th", {
                                          className: "px-4 py-3",
                                          children: e.seguridad.colWhat,
                                        }),
                                        i.jsx("th", {
                                          className: "px-4 py-3",
                                          children: e.seguridad.colBuyerType,
                                        }),
                                      ],
                                    }),
                                  }),
                                  i.jsxs("tbody", {
                                    className: "divide-y divide-gray-100",
                                    children: [
                                      (u.isLoading ||
                                        (u.isFetching && d.length === 0)) &&
                                        Array.from({ length: 5 }).map((x, b) =>
                                          i.jsx(ej, {}, b)
                                        ),
                                      d.map((x) =>
                                        i.jsxs(
                                          "tr",
                                          {
                                            className: "hover:bg-gray-50",
                                            children: [
                                              i.jsx("td", {
                                                className:
                                                  "px-4 py-3 text-gray-500",
                                                children: YC(x.createdAt),
                                              }),
                                              i.jsx("td", {
                                                className: "px-4 py-3",
                                                children: i.jsx("span", {
                                                  className: `rounded-full px-2 py-0.5 text-xs font-medium ${XC[x.source] ?? "bg-gray-100 text-gray-600"}`,
                                                  children:
                                                    a[x.source] ?? x.source,
                                                }),
                                              }),
                                              i.jsx("td", {
                                                className:
                                                  "px-4 py-3 text-gray-700",
                                                children: o(x.outcome ?? "—"),
                                              }),
                                              i.jsx("td", {
                                                className:
                                                  "px-4 py-3 text-gray-700",
                                                children: x.tool
                                                  ? x.tool.replace(/_/g, " ")
                                                  : "—",
                                              }),
                                              i.jsx("td", {
                                                className:
                                                  "px-4 py-3 text-xs text-gray-400",
                                                children: x.agentId
                                                  ? e.seguridad.buyerBot
                                                  : e.seguridad.buyerHuman,
                                              }),
                                            ],
                                          },
                                          x.id
                                        )
                                      ),
                                    ],
                                  }),
                                ],
                              }),
                            }),
                        u.data?.nextCursor &&
                          i.jsx("div", {
                            className: "mt-4 text-center",
                            children: i.jsx("button", {
                              onClick: () => r(u.data?.nextCursor ?? null),
                              disabled: u.isFetching,
                              className:
                                "text-sm text-blue-600 hover:text-blue-800 disabled:opacity-50",
                              children: u.isFetching
                                ? e.common.loadingMore
                                : e.seguridad.viewMoreActivity,
                            }),
                          }),
                      ],
                    }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    nj = {
      allow: "bg-green-100 text-green-700",
      probation: "bg-yellow-100 text-yellow-700",
      block: "bg-red-100 text-red-700",
      pending: "bg-gray-100 text-gray-500",
    },
    sj = { allow: "✅", probation: "👁️", block: "🚫", pending: "⏳" },
    aj = () =>
      i.jsx("tr", {
        children: Array.from({ length: 5 }).map((e, t) =>
          i.jsx(
            "td",
            {
              className: "px-4 py-3",
              children: i.jsx("div", {
                className: "h-4 animate-pulse rounded bg-gray-100",
              }),
            },
            t
          )
        ),
      }),
    oj = ({
      agent: e,
      isPending: t,
      onRevoke: r,
      onProbation: n,
      onAllow: s,
      t: a,
    }) => {
      const o = e.agenticTrustScore,
        l =
          o === null
            ? a.merchantAgentsPanel.noData
            : o >= 80
              ? a.merchantAgentsPanel.trustVeryReliable
              : o >= 50
                ? a.merchantAgentsPanel.trustAcceptable
                : a.merchantAgentsPanel.trustLow,
        c =
          o === null
            ? "#8c9196"
            : o >= 80
              ? "#1a7f4f"
              : o >= 50
                ? "#b27400"
                : "#c0392b",
        u = {
          allow: a.merchantAgentsPanel.stateCanBuy,
          probation: a.merchantAgentsPanel.stateWatching,
          block: a.merchantAgentsPanel.stateBanned,
          pending: a.merchantAgentsPanel.statePending,
        },
        d = e.state;
      return i.jsxs("tr", {
        className: "hover:bg-gray-50",
        children: [
          i.jsxs("td", {
            className: "px-4 py-3",
            children: [
              i.jsx("div", {
                className: "font-medium text-gray-800",
                children:
                  e.displayName ?? a.merchantAgentsPanel.defaultAgentName,
              }),
              i.jsxs("div", {
                className: "mt-0.5 text-xs text-gray-400",
                children: ["#", e.agentId.slice(0, 12)],
              }),
            ],
          }),
          i.jsx("td", {
            className: "px-4 py-3",
            children: i.jsxs("span", {
              className: `rounded-full px-2 py-0.5 text-xs font-medium ${nj[d] ?? "bg-gray-100 text-gray-500"}`,
              children: [sj[d] ?? "", " ", u[d] ?? d],
            }),
          }),
          i.jsx("td", {
            className: "px-4 py-3",
            children:
              e.agenticTrustScore !== null
                ? i.jsxs("div", {
                    children: [
                      i.jsxs("div", {
                        className: "text-sm font-bold",
                        style: { color: c },
                        children: [e.agenticTrustScore, "/100"],
                      }),
                      i.jsx("div", {
                        className: "text-xs",
                        style: { color: c },
                        children: l,
                      }),
                    ],
                  })
                : i.jsx("span", {
                    className: "text-xs text-gray-400",
                    children: a.merchantAgentsPanel.noData,
                  }),
          }),
          i.jsxs("td", {
            className: "px-4 py-3 text-sm text-gray-600",
            children: [
              i.jsx("div", {
                children: a.merchantAgentsPanel.callsLast30d.replace(
                  "{{count}}",
                  String(e.callsLast30d ?? 0)
                ),
              }),
              i.jsx("div", {
                className: "text-xs text-gray-400",
                children: a.merchantAgentsPanel.callsLast30dSuffix,
              }),
            ],
          }),
          i.jsx("td", {
            className: "px-4 py-3",
            children: i.jsxs("div", {
              className: "flex flex-col gap-1.5",
              children: [
                d !== "block" &&
                  i.jsx("button", {
                    onClick: r,
                    disabled: t,
                    className:
                      "w-full rounded border border-red-200 bg-red-50 px-2 py-1 text-left text-xs font-semibold text-red-700 hover:bg-red-100 disabled:opacity-60",
                    children: a.merchantAgentsPanel.revokeBtn,
                  }),
                (d === "allow" || d === "block") &&
                  i.jsx("button", {
                    onClick: n,
                    disabled: t,
                    className:
                      "w-full rounded border border-yellow-200 bg-yellow-50 px-2 py-1 text-left text-xs font-semibold text-yellow-700 hover:bg-yellow-100 disabled:opacity-60",
                    children: a.merchantAgentsPanel.probationBtn,
                  }),
                (d === "block" || d === "probation" || d === "pending") &&
                  i.jsx("button", {
                    onClick: s,
                    disabled: t,
                    className:
                      "w-full rounded border border-green-200 bg-green-50 px-2 py-1 text-left text-xs font-semibold text-green-700 hover:bg-green-100 disabled:opacity-60",
                    children: a.merchantAgentsPanel.allowBtn,
                  }),
              ],
            }),
          }),
        ],
      });
    },
    ay = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              t = fr(),
              [r, n] = R.useState([]),
              s = R.useRef(0),
              [a, o] = R.useState(new Set()),
              l = R.useCallback((w, S) => {
                const A = ++s.current;
                (n((N) => [...N, { id: A, message: w, type: S }]),
                  setTimeout(() => n((N) => N.filter((T) => T.id !== A)), 5e3));
              }, []),
              {
                data: c,
                isLoading: u,
                isError: d,
                error: f,
              } = _e({
                queryKey: ["merchant-agents"],
                queryFn: () => ae.get("/v1/embed/merchant/agents", wb),
              }),
              m = (w, S) => ({
                mutationFn: (A) =>
                  ae.post(
                    `/v1/embed/merchant/agents/${encodeURIComponent(A)}/${w}`,
                    {
                      reason: e.merchantAgentsPanel.auditReason.replace(
                        "{{action}}",
                        w
                      ),
                    },
                    _b
                  ),
                onMutate: (A) => {
                  o((N) => new Set([...N, A]));
                },
                onSuccess: (A, N) => {
                  (l(S, "success"),
                    o((T) => {
                      const _ = new Set(T);
                      return (_.delete(N), _);
                    }),
                    t.invalidateQueries({ queryKey: ["merchant-agents"] }));
                },
                onError: (A, N) => {
                  (o((T) => {
                    const _ = new Set(T);
                    return (_.delete(N), _);
                  }),
                    l(
                      A instanceof Error
                        ? A.message
                        : e.merchantAgentsPanel.toastError,
                      "error"
                    ));
                },
              }),
              v = Pt(m("revoke", e.merchantAgentsPanel.toastRevoked)),
              x = Pt(m("probation", e.merchantAgentsPanel.toastProbation)),
              b = Pt(m("allow", e.merchantAgentsPanel.toastAllowed)),
              j = c?.items ?? [],
              g = j.filter((w) => w.state === "allow").length,
              p = j.filter((w) => w.state === "probation").length,
              y = j.filter((w) => w.state === "block").length;
            return i.jsxs("div", {
              className: "p-6 max-w-5xl",
              children: [
                i.jsx("div", {
                  className: "fixed bottom-4 right-4 z-50 flex flex-col gap-2",
                  "aria-label": e.merchantAgentsPanel.notifications,
                  children: r.map((w) =>
                    i.jsxs(
                      "div",
                      {
                        role: "status",
                        "aria-live": "polite",
                        className: `flex items-center justify-between rounded-lg px-4 py-3 text-sm shadow-md ${w.type === "success" ? "bg-green-600 text-white" : "bg-red-600 text-white"}`,
                        children: [
                          i.jsx("span", { children: w.message }),
                          i.jsx("button", {
                            onClick: () =>
                              n((S) => S.filter((A) => A.id !== w.id)),
                            className: "ml-4 opacity-80 hover:opacity-100",
                            "aria-label": e.common.closeNotification,
                            children: "✕",
                          }),
                        ],
                      },
                      w.id
                    )
                  ),
                }),
                i.jsx("h1", {
                  className: "mb-2 text-xl font-semibold text-gray-900",
                  children: e.merchantAgentsPanel.title,
                }),
                i.jsx("p", {
                  className: "mb-4 text-sm text-gray-500",
                  children: e.merchantAgentsPanel.description,
                }),
                d &&
                  i.jsxs("div", {
                    className:
                      "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      e.merchantAgentsPanel.errorLoading,
                      " ",
                      f instanceof Error ? f.message : e.common.errorUnknown,
                    ],
                  }),
                !u &&
                  j.length > 0 &&
                  i.jsxs("div", {
                    className:
                      "mb-4 flex gap-6 rounded-lg border border-gray-200 bg-white px-6 py-4 shadow-sm",
                    children: [
                      i.jsxs("div", {
                        className: "text-center",
                        children: [
                          i.jsx("div", {
                            className: "text-2xl font-bold text-green-700",
                            children: g,
                          }),
                          i.jsx("div", {
                            className: "text-xs text-gray-500",
                            children: e.merchantAgentsPanel.countersCanBuy,
                          }),
                        ],
                      }),
                      i.jsxs("div", {
                        className: "text-center",
                        children: [
                          i.jsx("div", {
                            className: "text-2xl font-bold text-yellow-600",
                            children: p,
                          }),
                          i.jsx("div", {
                            className: "text-xs text-gray-500",
                            children: e.merchantAgentsPanel.countersWatching,
                          }),
                        ],
                      }),
                      i.jsxs("div", {
                        className: "text-center",
                        children: [
                          i.jsx("div", {
                            className: "text-2xl font-bold text-red-700",
                            children: y,
                          }),
                          i.jsx("div", {
                            className: "text-xs text-gray-500",
                            children: e.merchantAgentsPanel.countersBanned,
                          }),
                        ],
                      }),
                    ],
                  }),
                i.jsx("div", {
                  className:
                    "overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: i.jsxs("table", {
                    className: "min-w-full divide-y divide-gray-200 text-sm",
                    "aria-label": e.merchantAgentsPanel.tableLabel,
                    children: [
                      i.jsx("thead", {
                        children: i.jsxs("tr", {
                          className:
                            "text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                          children: [
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantAgentsPanel.colWho,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantAgentsPanel.colStatus,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantAgentsPanel.colReputation,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantAgentsPanel.colActivity,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantAgentsPanel.colActions,
                            }),
                          ],
                        }),
                      }),
                      i.jsxs("tbody", {
                        className: "divide-y divide-gray-100",
                        children: [
                          u &&
                            Array.from({ length: 4 }).map((w, S) =>
                              i.jsx(aj, {}, S)
                            ),
                          !u &&
                            j.map((w) =>
                              i.jsx(
                                oj,
                                {
                                  agent: w,
                                  isPending: a.has(w.agentId),
                                  onRevoke: () => v.mutate(w.agentId),
                                  onProbation: () => x.mutate(w.agentId),
                                  onAllow: () => b.mutate(w.agentId),
                                  t: e,
                                },
                                w.id
                              )
                            ),
                          !u &&
                            j.length === 0 &&
                            !d &&
                            i.jsx("tr", {
                              children: i.jsx("td", {
                                colSpan: 5,
                                className:
                                  "px-4 py-8 text-center text-sm text-gray-500",
                                children: i.jsxs("div", {
                                  className: "flex flex-col items-center gap-2",
                                  children: [
                                    i.jsx("span", {
                                      className: "text-3xl",
                                      children: "🤖",
                                    }),
                                    i.jsx("span", {
                                      className: "font-medium text-gray-700",
                                      children:
                                        e.merchantAgentsPanel.emptyTitle,
                                    }),
                                    i.jsx("span", {
                                      children: e.merchantAgentsPanel.emptyDesc,
                                    }),
                                  ],
                                }),
                              }),
                            }),
                        ],
                      }),
                    ],
                  }),
                }),
                i.jsxs("div", {
                  className:
                    "mt-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm",
                  children: [
                    i.jsx("h3", {
                      className: "mb-4 text-sm font-semibold text-gray-900",
                      children: e.merchantAgentsPanel.guideTitle,
                    }),
                    i.jsxs("div", {
                      className: "space-y-3",
                      children: [
                        i.jsxs("div", {
                          className: "flex items-start gap-3",
                          children: [
                            i.jsx("span", {
                              className: "text-lg",
                              children: "🟢",
                            }),
                            i.jsxs("div", {
                              className: "text-sm",
                              children: [
                                i.jsx("strong", {
                                  children: e.merchantAgentsPanel.guide80,
                                }),
                                " ",
                                i.jsx("span", {
                                  className: "text-gray-600",
                                  children: e.merchantAgentsPanel.guide80Desc,
                                }),
                              ],
                            }),
                          ],
                        }),
                        i.jsxs("div", {
                          className: "flex items-start gap-3",
                          children: [
                            i.jsx("span", {
                              className: "text-lg",
                              children: "🟡",
                            }),
                            i.jsxs("div", {
                              className: "text-sm",
                              children: [
                                i.jsx("strong", {
                                  children: e.merchantAgentsPanel.guide50,
                                }),
                                " ",
                                i.jsx("span", {
                                  className: "text-gray-600",
                                  children: e.merchantAgentsPanel.guide50Desc,
                                }),
                              ],
                            }),
                          ],
                        }),
                        i.jsxs("div", {
                          className: "flex items-start gap-3",
                          children: [
                            i.jsx("span", {
                              className: "text-lg",
                              children: "🔴",
                            }),
                            i.jsxs("div", {
                              className: "text-sm",
                              children: [
                                i.jsx("strong", {
                                  children: e.merchantAgentsPanel.guide0,
                                }),
                                " ",
                                i.jsx("span", {
                                  className: "text-gray-600",
                                  children: e.merchantAgentsPanel.guide0Desc,
                                }),
                              ],
                            }),
                          ],
                        }),
                      ],
                    }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    ij = pe.lazy(() => Te(() => Promise.resolve().then(() => sN), void 0)),
    lj = ({ loading: e }) =>
      i.jsx("div", { className: "p-6 text-sm text-gray-500", children: e }),
    cj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re();
            return i.jsxs("div", {
              className: "max-w-5xl",
              children: [
                i.jsx("h1", {
                  className: "mb-4 text-xl font-semibold text-gray-900",
                  children: e.trustCenter.title,
                }),
                i.jsx(R.Suspense, {
                  fallback: i.jsx(lj, { loading: e.trustCenter.loading }),
                  children: i.jsx(ij, {}),
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    uj = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    dj = (e) => {
      try {
        return uj.format(new Date(e));
      } catch {
        return e;
      }
    },
    pj = {
      SHOPIFY: "bg-blue-100 text-blue-700",
      WOO: "bg-orange-100 text-orange-700",
      ACP: "bg-purple-100 text-purple-700",
    },
    mj = {
      paid: "bg-green-100 text-green-700",
      fulfilled: "bg-teal-100 text-teal-700",
      cancelled: "bg-red-100 text-red-700",
      pending: "bg-gray-100 text-gray-600",
      created: "bg-gray-100 text-gray-600",
      updated: "bg-gray-100 text-gray-600",
      refunded: "bg-yellow-100 text-yellow-700",
    },
    oy = ({ label: e, classes: t }) =>
      i.jsx("span", {
        className: `rounded-full px-2 py-0.5 text-xs font-medium ${t}`,
        children: e,
      }),
    fj = () =>
      i.jsx("tr", {
        children: Array.from({ length: 5 }).map((e, t) =>
          i.jsx(
            "td",
            {
              className: "px-4 py-3",
              children: i.jsx("div", {
                className: "h-4 animate-pulse rounded bg-gray-100",
              }),
            },
            t
          )
        ),
      }),
    hj = (e) => {
      const t = new URLSearchParams({ limit: "20" });
      return (
        e !== null && t.set("cursor", e),
        `/v1/embed/merchant/orders?${t.toString()}`
      );
    },
    gj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              [t, r] = R.useState(null),
              [n, s] = R.useState([]),
              a = hj(t),
              {
                data: o,
                isLoading: l,
                isError: c,
                error: u,
              } = _e({
                queryKey: ["merchant-orders", t],
                queryFn: () => ae.get(a, zh),
                placeholderData: (f) => f,
              });
            pe.useEffect(() => {
              o?.items &&
                s(
                  t === null
                    ? o.items
                    : (f) => {
                        const m = new Set(f.map((v) => v.id));
                        return [...f, ...o.items.filter((v) => !m.has(v.id))];
                      }
                );
            }, [o, t]);
            const d = n.length > 0 ? n : (o?.items ?? []);
            return i.jsxs("div", {
              className: "p-6 max-w-5xl",
              children: [
                c &&
                  i.jsxs("div", {
                    className:
                      "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      e.merchantOrders.errorLoading,
                      " ",
                      u instanceof Error ? u.message : e.common.errorUnknown,
                    ],
                  }),
                i.jsx("div", {
                  className:
                    "overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: i.jsxs("table", {
                    className: "min-w-full divide-y divide-gray-200 text-sm",
                    "aria-label": e.merchantOrders.tableLabel,
                    children: [
                      i.jsx("thead", {
                        children: i.jsxs("tr", {
                          className:
                            "text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                          children: [
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantOrders.colPlatform,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantOrders.colOrderId,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantOrders.colStatus,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantOrders.colTotal,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.merchantOrders.colDate,
                            }),
                          ],
                        }),
                      }),
                      i.jsxs("tbody", {
                        className: "divide-y divide-gray-100",
                        children: [
                          l &&
                            Array.from({ length: 5 }).map((f, m) =>
                              i.jsx(fj, {}, m)
                            ),
                          !l &&
                            d.map((f) =>
                              i.jsxs(
                                "tr",
                                {
                                  className: "hover:bg-gray-50",
                                  children: [
                                    i.jsx("td", {
                                      className: "px-4 py-3",
                                      children: i.jsx(oy, {
                                        label: f.platform,
                                        classes:
                                          pj[f.platform] ??
                                          "bg-gray-100 text-gray-700",
                                      }),
                                    }),
                                    i.jsx("td", {
                                      className:
                                        "px-4 py-3 font-mono text-xs text-gray-700",
                                      children:
                                        f.externalOrderId.length > 16
                                          ? `${f.externalOrderId.slice(0, 16)}…`
                                          : f.externalOrderId,
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-3",
                                      children: i.jsx(oy, {
                                        label: f.status,
                                        classes:
                                          mj[f.status] ??
                                          "bg-gray-100 text-gray-600",
                                      }),
                                    }),
                                    i.jsxs("td", {
                                      className: "px-4 py-3 text-gray-700",
                                      children: [
                                        f.total.amount,
                                        " ",
                                        f.total.currency,
                                      ],
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-3 text-gray-500",
                                      children: dj(f.createdAt),
                                    }),
                                  ],
                                },
                                f.id
                              )
                            ),
                          !l &&
                            d.length === 0 &&
                            !c &&
                            i.jsx("tr", {
                              children: i.jsx("td", {
                                colSpan: 5,
                                className:
                                  "px-4 py-6 text-center text-sm text-gray-500",
                                children: e.merchantOrders.noOrders,
                              }),
                            }),
                        ],
                      }),
                    ],
                  }),
                }),
                o?.nextCursor !== null &&
                  o?.nextCursor !== void 0 &&
                  i.jsx("div", {
                    className: "mt-4 flex justify-center",
                    children: i.jsx("button", {
                      onClick: () => {
                        o.nextCursor !== null && r(o.nextCursor);
                      },
                      className:
                        "rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500",
                      children: e.merchantOrders.loadMore,
                    }),
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    yj = {
      "stripe-connect": "Stripe Connect",
      x402: "x402",
      acp: "ACP",
      paypal: "PayPal",
      eidas: "eIDAS",
    },
    vj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              {
                data: t,
                isLoading: r,
                isError: n,
                error: s,
              } = _e({
                queryKey: ["merchant-checkout-config"],
                queryFn: () => ae.get("/v1/embed/merchant/checkout/config", Fh),
              }),
              a = new Set(
                (t?.rules ?? []).filter((l) => l.enabled).map((l) => l.rail)
              ),
              o =
                (t?.railsPriority?.length ?? 0) > 0
                  ? (t?.railsPriority ?? [])
                  : (t?.rules ?? []).map((l) => l.rail);
            return i.jsxs("div", {
              className: "p-6 max-w-2xl",
              children: [
                i.jsx("p", {
                  className: "mb-5 text-sm text-gray-500",
                  children: e.merchantCheckoutConfig.description,
                }),
                n &&
                  i.jsxs("div", {
                    className:
                      "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      e.merchantCheckoutConfig.errorLoading,
                      " ",
                      s instanceof Error ? s.message : e.common.errorUnknown,
                    ],
                  }),
                r &&
                  i.jsx("div", {
                    className: "space-y-3",
                    role: "status",
                    "aria-label": e.merchantCheckoutConfig.ariaLoading,
                    children: Array.from({ length: 5 }).map((l, c) =>
                      i.jsx(
                        "div",
                        {
                          className:
                            "h-12 animate-pulse rounded-lg bg-gray-100",
                        },
                        c
                      )
                    ),
                  }),
                !r &&
                  !n &&
                  i.jsx("ul", {
                    className: "space-y-3",
                    "aria-label": e.merchantCheckoutConfig.railsAriaLabel,
                    children: o.map((l) => {
                      const c = a.has(l);
                      return i.jsxs(
                        "li",
                        {
                          className:
                            "flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm",
                          children: [
                            i.jsx("span", {
                              className: "font-medium text-gray-800",
                              children: yj[l] ?? l,
                            }),
                            i.jsx("span", {
                              className: `rounded-full px-2.5 py-0.5 text-xs font-medium ${c ? "bg-green-100 text-green-700" : "bg-gray-100 text-gray-500"}`,
                              children: c
                                ? e.merchantCheckoutConfig.enabled
                                : e.merchantCheckoutConfig.disabled,
                            }),
                          ],
                        },
                        l
                      );
                    }),
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    xj = {
      active: "bg-green-100 text-green-700",
      expiring_soon: "bg-yellow-100 text-yellow-700",
      expired: "bg-red-100 text-red-700",
      pending: "bg-gray-100 text-gray-500",
      not_applicable: "bg-gray-50 text-gray-400",
    },
    bj = {
      idle: "bg-gray-100 text-gray-600",
      indexing: "bg-blue-100 text-blue-700",
      error: "bg-red-100 text-red-700",
      never_indexed: "bg-gray-50 text-gray-400",
    },
    wj = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    iy = (e) => {
      if (e === null) return "—";
      try {
        return wj.format(new Date(e));
      } catch {
        return e;
      }
    },
    Ts = ({ title: e, children: t }) =>
      i.jsxs("div", {
        className: "rounded-lg border border-gray-200 bg-white p-5 shadow-sm",
        children: [
          i.jsx("h3", {
            className: "mb-4 text-base font-semibold text-gray-900",
            children: e,
          }),
          t,
        ],
      }),
    rd = ({ label: e, classes: t }) =>
      i.jsx("span", {
        className: `rounded-full px-2 py-0.5 text-xs font-medium ${t}`,
        children: e,
      }),
    nd = () =>
      i.jsx("div", { className: "h-40 animate-pulse rounded-lg bg-gray-100" }),
    kj = ({ t: e }) => {
      const {
        data: t,
        isLoading: r,
        isError: n,
      } = _e({
        queryKey: ["merchant-certification"],
        queryFn: () => ae.get("/v1/embed/merchant/certification", Cb),
      });
      if (r) return i.jsx(nd, {});
      if (n)
        return i.jsx(Ts, {
          title: e.merchantCertNlweb.certTitle,
          children: i.jsx("p", {
            className: "text-sm text-red-600",
            children: e.merchantCertNlweb.errorCert,
          }),
        });
      const s = [
        { key: "eidas", label: "eIDAS" },
        { key: "kyc", label: "KYC" },
        { key: "schemaOrg", label: "Schema.org" },
      ];
      return i.jsx(Ts, {
        title: e.merchantCertNlweb.certTitle,
        children: i.jsx("ul", {
          className: "space-y-3",
          children: s.map(({ key: a, label: o }) => {
            const l = t?.[a],
              c = e.merchantCertNlweb.certStatusLabels,
              u = l?.status ?? "";
            return i.jsxs(
              "li",
              {
                className: "flex items-center justify-between",
                children: [
                  i.jsx("span", {
                    className: "text-sm text-gray-700",
                    children: o,
                  }),
                  i.jsx(rd, {
                    label: c[u] ?? (u || "—"),
                    classes: xj[u] ?? "bg-gray-100 text-gray-500",
                  }),
                ],
              },
              a
            );
          }),
        }),
      });
    },
    _j = ({ onToast: e, t }) => {
      const {
          data: r,
          isLoading: n,
          isError: s,
        } = _e({
          queryKey: ["merchant-nlweb-status"],
          queryFn: () => ae.get("/v1/embed/merchant/nlweb/status", Ab),
        }),
        a = Pt({
          mutationFn: () => ae.post("/v1/embed/merchant/nlweb/reindex", {}, Eb),
          onSuccess: () =>
            e(t.merchantCertNlweb.nlwebReindexSuccess, "success"),
          onError: (o) => {
            const l =
              o instanceof Error
                ? o.message
                : t.merchantCertNlweb.nlwebReindexError;
            e(l, "error");
          },
        });
      return n
        ? i.jsx(nd, {})
        : s
          ? i.jsx(Ts, {
              title: t.merchantCertNlweb.nlwebTitle,
              children: i.jsx("p", {
                className: "text-sm text-red-600",
                children: t.merchantCertNlweb.errorNlweb,
              }),
            })
          : i.jsxs(Ts, {
              title: t.merchantCertNlweb.nlwebTitle,
              children: [
                i.jsxs("div", {
                  className: "space-y-3 text-sm",
                  children: [
                    i.jsxs("div", {
                      className: "flex items-center justify-between",
                      children: [
                        i.jsx("span", {
                          className: "text-gray-600",
                          children: t.merchantCertNlweb.nlwebStatus,
                        }),
                        i.jsx(rd, {
                          label:
                            t.merchantCertNlweb.nlwebStatusLabels[
                              r?.status ?? ""
                            ] ??
                            (r?.status || "—"),
                          classes:
                            bj[r?.status ?? ""] ?? "bg-gray-100 text-gray-500",
                        }),
                      ],
                    }),
                    i.jsxs("div", {
                      className: "flex items-center justify-between",
                      children: [
                        i.jsx("span", {
                          className: "text-gray-600",
                          children: t.merchantCertNlweb.nlwebLastIndex,
                        }),
                        i.jsx("span", {
                          className: "text-gray-700",
                          children: iy(r?.lastIndexedAt ?? null),
                        }),
                      ],
                    }),
                    i.jsxs("div", {
                      className: "flex items-center justify-between",
                      children: [
                        i.jsx("span", {
                          className: "text-gray-600",
                          children: t.merchantCertNlweb.nlwebProductsIndexed,
                        }),
                        i.jsx("span", {
                          className: "text-gray-700",
                          children: r?.productsIndexed ?? 0,
                        }),
                      ],
                    }),
                    r?.lastError !== null &&
                      r?.lastError !== void 0 &&
                      i.jsx("p", {
                        className: "text-xs text-red-600",
                        children: r.lastError,
                      }),
                  ],
                }),
                i.jsx("button", {
                  onClick: () => a.mutate(),
                  disabled: a.isPending || r?.status === "indexing",
                  className:
                    "mt-4 rounded bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 disabled:opacity-60",
                  children: a.isPending
                    ? t.merchantCertNlweb.nlwebReindexing
                    : t.merchantCertNlweb.nlwebReindex,
                }),
              ],
            });
    },
    Sj = ({ t: e }) => {
      const {
        data: t,
        isLoading: r,
        isError: n,
      } = _e({
        queryKey: ["merchant-webmcp"],
        queryFn: () => ae.get("/v1/embed/merchant/webmcp", Nb),
      });
      return r
        ? i.jsx(nd, {})
        : n
          ? i.jsx(Ts, {
              title: e.merchantCertNlweb.webmcpTitle,
              children: i.jsx("p", {
                className: "text-sm text-red-600",
                children: e.merchantCertNlweb.errorWebmcp,
              }),
            })
          : i.jsx(Ts, {
              title: e.merchantCertNlweb.webmcpTitle,
              children: i.jsxs("div", {
                className: "space-y-3 text-sm",
                children: [
                  i.jsxs("div", {
                    className: "flex items-center justify-between",
                    children: [
                      i.jsx("span", {
                        className: "text-gray-600",
                        children: e.merchantCertNlweb.webmcpStatus,
                      }),
                      i.jsx(rd, {
                        label: t?.enabled
                          ? e.merchantCertNlweb.webmcpEnabled
                          : e.merchantCertNlweb.webmcpDisabled,
                        classes: t?.enabled
                          ? "bg-green-100 text-green-700"
                          : "bg-gray-100 text-gray-500",
                      }),
                    ],
                  }),
                  i.jsxs("div", {
                    className: "flex items-center justify-between",
                    children: [
                      i.jsx("span", {
                        className: "text-gray-600",
                        children: e.merchantCertNlweb.webmcpAdapter,
                      }),
                      i.jsx("span", {
                        className: "text-gray-700",
                        children: t?.detectedAdapter ?? "—",
                      }),
                    ],
                  }),
                  i.jsxs("div", {
                    className: "flex items-center justify-between",
                    children: [
                      i.jsx("span", {
                        className: "text-gray-600",
                        children: e.merchantCertNlweb.webmcpLastConnection,
                      }),
                      i.jsx("span", {
                        className: "text-gray-700",
                        children: iy(t?.bridgeLastSeenAt ?? null),
                      }),
                    ],
                  }),
                ],
              }),
            });
    },
    Cj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              [t, r] = R.useState([]),
              n = R.useRef(0),
              s = R.useCallback((a, o) => {
                const l = ++n.current;
                (r((c) => [...c, { id: l, message: a, type: o }]),
                  setTimeout(() => {
                    r((c) => c.filter((u) => u.id !== l));
                  }, 5e3));
              }, []);
            return i.jsxs("div", {
              className: "p-6 max-w-2xl",
              children: [
                i.jsx("div", {
                  className: "fixed bottom-4 right-4 z-50 flex flex-col gap-2",
                  "aria-label": e.merchantCertNlweb.notifications,
                  children: t.map((a) =>
                    i.jsxs(
                      "div",
                      {
                        role: "status",
                        "aria-live": "polite",
                        className: `flex items-center justify-between rounded-lg px-4 py-3 text-sm shadow-md ${a.type === "success" ? "bg-green-600 text-white" : "bg-red-600 text-white"}`,
                        children: [
                          i.jsx("span", { children: a.message }),
                          i.jsx("button", {
                            onClick: () =>
                              r((o) => o.filter((l) => l.id !== a.id)),
                            className: "ml-4 opacity-80 hover:opacity-100",
                            "aria-label": e.merchantCertNlweb.closeNotification,
                            children: "✕",
                          }),
                        ],
                      },
                      a.id
                    )
                  ),
                }),
                i.jsxs("div", {
                  className: "flex flex-col gap-4",
                  children: [
                    i.jsx(kj, { t: e }),
                    i.jsx(_j, { onToast: s, t: e }),
                    i.jsx(Sj, { t: e }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    jj = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    Nj = (e) => {
      try {
        return jj.format(new Date(e));
      } catch {
        return e;
      }
    },
    Rj = {
      discovery: "bg-blue-50 text-blue-700",
      customer: "bg-purple-50 text-purple-700",
      checkout: "bg-green-50 text-green-700",
    },
    Aj = () =>
      i.jsx("tr", {
        children: Array.from({ length: 5 }).map((e, t) =>
          i.jsx(
            "td",
            {
              className: "py-2 pr-4",
              children: i.jsx("div", {
                className: "h-4 animate-pulse rounded bg-gray-100",
              }),
            },
            t
          )
        ),
      }),
    Ej = (e, t, r, n, s) => {
      const a = new URLSearchParams();
      return (
        a.set("limit", "20"),
        e && a.set("agentId", e),
        t !== "todos" && a.set("bucket", t),
        r && a.set("from", new Date(r).toISOString()),
        n && a.set("to", new Date(n).toISOString()),
        s && a.set("cursor", s),
        `/v1/embed/trust/receipts?${a.toString()}`
      );
    },
    Tj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: ({ onSelect: e }) => {
            const t = Re(),
              r = {
                discovery: t.trustReceiptsList.bucketDiscovery,
                customer: t.trustReceiptsList.bucketCustomer,
                checkout: t.trustReceiptsList.bucketCheckout,
              },
              n = ({ bucket: _ }) =>
                i.jsx("span", {
                  className: `rounded-full px-2 py-0.5 text-xs font-medium ${Rj[_] ?? "bg-gray-100 text-gray-700"}`,
                  children: r[_] ?? _,
                }),
              [s, a] = R.useState(""),
              [o, l] = R.useState("todos"),
              [c, u] = R.useState(""),
              [d, f] = R.useState(""),
              [m, v] = R.useState(null),
              [x, b] = R.useState([]),
              j = Ej(s, o, c, d, m),
              {
                data: g,
                isLoading: p,
                isError: y,
                error: w,
              } = _e({
                queryKey: ["trust-receipts", s, o, c, d, m],
                queryFn: () => ae.get(j, cb),
                placeholderData: (_) => _,
              }),
              S = R.useCallback((_, O) => {
                (_(O), v(null), b([]));
              }, []),
              A = R.useCallback((_) => {
                (l(_), v(null), b([]));
              }, []);
            pe.useEffect(() => {
              g?.items &&
                b(
                  m === null
                    ? g.items
                    : (_) => {
                        const O = new Set(_.map((ee) => ee.id)),
                          q = g.items.filter((ee) => !O.has(ee.id));
                        return [..._, ...q];
                      }
                );
            }, [g, m]);
            const N = () => {
                g?.nextCursor && v(g.nextCursor);
              },
              T = x.length > 0 ? x : (g?.items ?? []);
            return i.jsxs("div", {
              className: "p-6 max-w-4xl",
              children: [
                i.jsx("div", {
                  className:
                    "mb-3 rounded-lg border border-blue-100 bg-blue-50 px-5 py-4",
                  children: i.jsx("p", {
                    className: "text-sm leading-relaxed text-blue-800",
                    children: t.trustReceiptsList.description,
                  }),
                }),
                i.jsx("div", {
                  className:
                    "mb-3 rounded-lg border border-green-100 bg-green-50 px-5 py-4",
                  children: i.jsx("p", {
                    className: "text-sm leading-relaxed text-green-800",
                    children: t.trustReceiptsList.pacoBenefits,
                  }),
                }),
                i.jsx("div", {
                  className:
                    "mb-3 rounded-lg border border-amber-100 bg-amber-50 px-5 py-4",
                  children: i.jsx("p", {
                    className: "text-sm leading-relaxed text-amber-800",
                    children: t.trustReceiptsList.pacoRegulation,
                  }),
                }),
                i.jsx("div", {
                  className: "mb-5 text-right",
                  children: i.jsxs("a", {
                    href: "https://trusteed.xyz/es/trust/trust-receipt",
                    target: "_blank",
                    rel: "noopener noreferrer",
                    className:
                      "text-sm text-indigo-600 underline hover:text-indigo-800",
                    children: [t.trustReceiptsList.learnMore, " →"],
                  }),
                }),
                i.jsxs("div", {
                  className: "mb-4 flex flex-wrap gap-3",
                  children: [
                    i.jsx("input", {
                      type: "text",
                      placeholder: t.trustReceiptsList.filterBuyerId,
                      value: s,
                      onChange: (_) => S(a, _.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": t.trustReceiptsList.filterBuyerIdAria,
                    }),
                    i.jsxs("select", {
                      value: o,
                      onChange: (_) => A(_.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": t.trustReceiptsList.filterTypeAria,
                      children: [
                        i.jsx("option", {
                          value: "todos",
                          children: t.trustReceiptsList.filterTypeTodos,
                        }),
                        i.jsx("option", {
                          value: "discovery",
                          children: t.trustReceiptsList.filterTypeDiscovery,
                        }),
                        i.jsx("option", {
                          value: "customer",
                          children: t.trustReceiptsList.filterTypeCustomer,
                        }),
                        i.jsx("option", {
                          value: "checkout",
                          children: t.trustReceiptsList.filterTypeCheckout,
                        }),
                      ],
                    }),
                    i.jsx("input", {
                      type: "date",
                      value: c,
                      onChange: (_) => S(u, _.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": t.trustReceiptsList.filterDateFrom,
                    }),
                    i.jsx("input", {
                      type: "date",
                      value: d,
                      onChange: (_) => S(f, _.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": t.trustReceiptsList.filterDateTo,
                    }),
                  ],
                }),
                y &&
                  i.jsxs("div", {
                    className:
                      "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      t.trustReceiptsList.errorLoading,
                      " ",
                      w instanceof Error ? w.message : t.common.errorUnknown,
                    ],
                  }),
                i.jsx("div", {
                  className:
                    "overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: i.jsxs("table", {
                    className: "min-w-full divide-y divide-gray-200 text-sm",
                    "aria-label": t.trustReceiptsList.tableLabel,
                    children: [
                      i.jsx("thead", {
                        children: i.jsxs("tr", {
                          className:
                            "text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                          children: [
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: t.trustReceiptsList.colNumber,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: t.trustReceiptsList.colOperation,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: t.trustReceiptsList.colType,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: t.trustReceiptsList.colBuyer,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: t.trustReceiptsList.colDate,
                            }),
                          ],
                        }),
                      }),
                      i.jsxs("tbody", {
                        className: "divide-y divide-gray-100",
                        children: [
                          p &&
                            Array.from({ length: 5 }).map((_, O) =>
                              i.jsx(Aj, {}, O)
                            ),
                          !p &&
                            T.map((_) =>
                              i.jsxs(
                                "tr",
                                {
                                  onClick: () => e(_),
                                  className:
                                    "cursor-pointer hover:bg-gray-50 transition-colors",
                                  tabIndex: 0,
                                  onKeyDown: (O) => {
                                    (O.key === "Enter" || O.key === " ") &&
                                      e(_);
                                  },
                                  children: [
                                    i.jsxs("td", {
                                      className:
                                        "px-4 py-2 font-mono text-xs text-gray-700",
                                      children: [_.id.slice(0, 12), "..."],
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-700",
                                      children: _.tool,
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2",
                                      children: i.jsx(n, { bucket: _.bucket }),
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-700",
                                      children: _.agentId,
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-500",
                                      children: Nj(_.createdAt),
                                    }),
                                  ],
                                },
                                _.id
                              )
                            ),
                          !p &&
                            T.length === 0 &&
                            !y &&
                            i.jsx("tr", {
                              children: i.jsx("td", {
                                colSpan: 5,
                                className:
                                  "px-4 py-6 text-center text-sm text-gray-500",
                                children: t.trustReceiptsList.noEntries,
                              }),
                            }),
                        ],
                      }),
                    ],
                  }),
                }),
                g?.nextCursor &&
                  i.jsx("div", {
                    className: "mt-4 flex justify-center",
                    children: i.jsx("button", {
                      onClick: N,
                      className:
                        "rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500",
                      children: t.trustReceiptsList.loadMore,
                    }),
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    );
  async function Pj() {
    const e = await Cs.getToken();
    if (e !== null) return e;
    try {
      return await Cs.refresh();
    } catch {
      return null;
    }
  }
  function Oj(e, t) {
    const r = URL.createObjectURL(e),
      n = document.createElement("a");
    ((n.href = r),
      (n.download = t),
      document.body.appendChild(n),
      n.click(),
      n.remove(),
      URL.revokeObjectURL(r));
  }
  const Ij = ({ receiptId: e }) => {
      const t = Re(),
        [r, n] = R.useState(!1),
        [s, a] = R.useState(null),
        o = R.useCallback(async () => {
          (n(!0), a(null));
          try {
            const l = await Pj(),
              c = await fetch(
                `${Zb()}/v1/embed/trust/export/${encodeURIComponent(e)}`,
                {
                  method: "GET",
                  headers: {
                    "X-Embed-Source": Wh(),
                    ...(l ? { Authorization: `Bearer ${l}` } : {}),
                  },
                }
              );
            if (c.status === 400) {
              a(t.trustReceiptDetail.downloadErrorV10);
              return;
            }
            if (c.status === 409) {
              a(t.trustReceiptDetail.downloadErrorLegacy);
              return;
            }
            if (!c.ok) {
              a(t.trustReceiptDetail.downloadErrorGeneric);
              return;
            }
            const u = await c.blob();
            Oj(u, `trust-receipt-${e}.zip`);
          } catch {
            a(t.trustReceiptDetail.downloadErrorGeneric);
          } finally {
            n(!1);
          }
        }, [e, t]);
      return i.jsxs("div", {
        className: "space-y-2",
        children: [
          i.jsx("button", {
            onClick: () => {
              o();
            },
            disabled: r,
            className:
              "w-full rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60",
            children: r
              ? t.trustReceiptDetail.downloading
              : t.trustReceiptDetail.downloadBundle,
          }),
          i.jsx("p", {
            className: "text-xs text-gray-500",
            children: t.trustReceiptDetail.downloadHint,
          }),
          s
            ? i.jsx("p", {
                className: "text-xs text-red-700",
                role: "alert",
                children: s,
              })
            : null,
        ],
      });
    },
    Lj = (() => {
      try {
        const e = window;
        return (
          e.__AMCP_CONFIG__?.locale ??
          e.__AMCP_PS_CONFIG__?.locale ??
          navigator.language ??
          "en"
        )
          .split(/[-_]/)[0]
          .toLowerCase() === "es"
          ? "es-ES"
          : "en-GB";
      } catch {
        return "en-GB";
      }
    })(),
    Dj = new Intl.DateTimeFormat(Lj, {
      dateStyle: "medium",
      timeStyle: "medium",
      timeZone: "UTC",
    }),
    zj = (e) => {
      try {
        return `${Dj.format(new Date(e))} UTC`;
      } catch {
        return e;
      }
    },
    Mj = {
      discovery: "bg-blue-50 text-blue-700",
      customer: "bg-purple-50 text-purple-700",
      checkout: "bg-green-50 text-green-700",
    },
    Yr = ({ label: e, value: t }) =>
      i.jsxs("div", {
        className: "py-2",
        children: [
          i.jsx("dt", {
            className:
              "text-xs font-medium uppercase tracking-wide text-gray-500",
            children: e,
          }),
          i.jsx("dd", {
            className: "mt-1 text-sm text-gray-900 break-all",
            children: t,
          }),
        ],
      }),
    $j = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: ({ receipt: e, onClose: t }) => {
            const r = Re(),
              [n, s] = R.useState(!1),
              [a, o] = R.useState(!1),
              l = R.useRef(null);
            R.useEffect(() => {
              const d = (f) => {
                f.key === "Escape" && t();
              };
              return (
                document.addEventListener("keydown", d),
                () => document.removeEventListener("keydown", d)
              );
            }, [t]);
            const c = R.useCallback(
                (d) => {
                  l.current && !l.current.contains(d.target) && t();
                },
                [t]
              ),
              u = R.useCallback(async () => {
                try {
                  (await navigator.clipboard.writeText(e.jwksSnapshotUrl ?? ""),
                    o(!0),
                    setTimeout(() => o(!1), 2e3));
                } catch {}
              }, [e.jwksSnapshotUrl]);
            return i.jsx("div", {
              className: "fixed inset-0 z-50 flex justify-end bg-black/30",
              onClick: c,
              "aria-modal": "true",
              role: "dialog",
              "aria-label": r.trustReceiptDetail.ariaLabel,
              children: i.jsxs("div", {
                ref: l,
                className:
                  "h-full w-full max-w-md overflow-y-auto bg-white shadow-xl",
                children: [
                  i.jsxs("div", {
                    className:
                      "flex items-center justify-between border-b border-gray-200 px-6 py-4",
                    children: [
                      i.jsx("h2", {
                        className: "text-lg font-semibold text-gray-900",
                        children: r.trustReceiptDetail.title,
                      }),
                      i.jsx("button", {
                        onClick: t,
                        className:
                          "rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500",
                        "aria-label": r.trustReceiptDetail.closeAriaLabel,
                        children: i.jsx("svg", {
                          className: "h-5 w-5",
                          fill: "none",
                          viewBox: "0 0 24 24",
                          stroke: "currentColor",
                          strokeWidth: 2,
                          children: i.jsx("path", {
                            strokeLinecap: "round",
                            strokeLinejoin: "round",
                            d: "M6 18L18 6M6 6l12 12",
                          }),
                        }),
                      }),
                    ],
                  }),
                  i.jsxs("div", {
                    className: "px-6 py-4",
                    children: [
                      i.jsxs("dl", {
                        className: "divide-y divide-gray-100",
                        children: [
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldReceiptId,
                            value: e.id,
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldBucket,
                            value: i.jsx("span", {
                              className: `inline-block rounded-full px-2 py-0.5 text-xs font-medium ${Mj[e.bucket] ?? "bg-gray-100 text-gray-700"}`,
                              children: e.bucket,
                            }),
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldTool,
                            value: e.tool,
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldAgentId,
                            value: e.agentId,
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldSigningKey,
                            value: e.signingKeyKid,
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldDate,
                            value: zj(e.createdAt),
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldInputHash,
                            value: i.jsx("span", {
                              className: "font-mono text-xs",
                              children: e.inputHash,
                            }),
                          }),
                          i.jsx(Yr, {
                            label: r.trustReceiptDetail.fieldOutputHash,
                            value: i.jsx("span", {
                              className: "font-mono text-xs",
                              children: e.outputHash,
                            }),
                          }),
                        ],
                      }),
                      i.jsxs("div", {
                        className: "mt-4",
                        children: [
                          i.jsx("button", {
                            onClick: () => s((d) => !d),
                            className:
                              "text-sm text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500",
                            children: n
                              ? r.trustReceiptDetail.hideJws
                              : r.trustReceiptDetail.showJws,
                          }),
                          n &&
                            i.jsxs("pre", {
                              className:
                                "mt-2 overflow-auto rounded bg-gray-50 p-3 text-xs text-gray-700 max-h-48",
                              children: [
                                e.jws.slice(0, 300),
                                e.jws.length > 300 ? "..." : "",
                              ],
                            }),
                        ],
                      }),
                      e.jwksSnapshotUrl &&
                        i.jsx("div", {
                          className: "mt-4",
                          children: i.jsx("button", {
                            onClick: () => {
                              u();
                            },
                            className:
                              "rounded border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500",
                            children: a
                              ? r.trustReceiptDetail.copied
                              : r.trustReceiptDetail.copyJwksUrl,
                          }),
                        }),
                    ],
                  }),
                  i.jsxs("div", {
                    className: "space-y-3 border-t border-gray-200 px-6 py-4",
                    children: [
                      i.jsx(Ij, { receiptId: e.id }),
                      i.jsx("button", {
                        onClick: t,
                        className:
                          "w-full rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500",
                        children: r.trustReceiptDetail.closeBtn,
                      }),
                    ],
                  }),
                ],
              }),
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    Fj = new Intl.DateTimeFormat("es-ES", {
      dateStyle: "medium",
      timeZone: "UTC",
    }),
    Uj = (e) => {
      if (!e) return "—";
      try {
        return Fj.format(new Date(e));
      } catch {
        return e;
      }
    },
    Zj = ({ ariaLabel: e }) =>
      i.jsx("div", {
        className: "h-24 animate-pulse rounded-lg bg-gray-100",
        role: "status",
        "aria-label": e,
      }),
    Bj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              {
                data: t,
                isLoading: r,
                isError: n,
                error: s,
              } = _e({
                queryKey: ["trust-keys"],
                queryFn: async () => {
                  try {
                    return await ae.get("/v1/embed/trust/keys", Fa);
                  } catch (l) {
                    if (l instanceof Bt && l.status === 404) return null;
                    throw l;
                  }
                },
                retry: !1,
              }),
              a = !r && !n && t === null,
              o = (t?.daysSinceRotation ?? 0) > 90;
            return i.jsxs("div", {
              className: "p-6 max-w-2xl",
              children: [
                i.jsx("div", {
                  className:
                    "mb-5 rounded-lg border border-green-100 bg-green-50 px-5 py-4",
                  children: i.jsx("p", {
                    className: "text-sm leading-relaxed text-green-800",
                    children: e.trustKeys.description,
                  }),
                }),
                r && i.jsx(Zj, { ariaLabel: e.trustKeys.ariaLoading }),
                n &&
                  i.jsxs("div", {
                    className:
                      "rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      e.trustKeys.errorLoading,
                      " ",
                      s instanceof Error ? s.message : e.common.errorUnknown,
                    ],
                  }),
                a &&
                  i.jsxs("div", {
                    className:
                      "rounded-lg border border-blue-100 bg-blue-50 px-5 py-4",
                    role: "status",
                    children: [
                      i.jsx("p", {
                        className: "text-sm font-semibold text-blue-900",
                        children: e.trustKeys.notActivatedTitle,
                      }),
                      i.jsx("p", {
                        className: "mt-1 text-sm text-blue-800",
                        children: e.trustKeys.notActivatedDesc,
                      }),
                    ],
                  }),
                t &&
                  i.jsxs(i.Fragment, {
                    children: [
                      o &&
                        i.jsx("div", {
                          className:
                            "mb-4 rounded border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800",
                          role: "status",
                          children: e.trustKeys.overdueWarning.replace(
                            "{{days}}",
                            String(t.daysSinceRotation)
                          ),
                        }),
                      i.jsxs("div", {
                        className:
                          "mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm",
                        children: [
                          i.jsx("h3", {
                            className:
                              "mb-3 text-base font-semibold text-gray-900",
                            children: e.trustKeys.activeKeyTitle,
                          }),
                          i.jsxs("dl", {
                            className: "space-y-2 text-sm",
                            children: [
                              i.jsxs("div", {
                                className: "flex gap-2",
                                children: [
                                  i.jsx("dt", {
                                    className:
                                      "w-40 shrink-0 font-medium text-gray-500",
                                    children: e.trustKeys.fieldIdentifier,
                                  }),
                                  i.jsx("dd", {
                                    className: "font-mono text-gray-900",
                                    children: t.activeKid,
                                  }),
                                ],
                              }),
                              i.jsxs("div", {
                                className: "flex gap-2",
                                children: [
                                  i.jsx("dt", {
                                    className:
                                      "w-40 shrink-0 font-medium text-gray-500",
                                    children: e.trustKeys.fieldLastRenewal,
                                  }),
                                  i.jsx("dd", {
                                    className: "text-gray-900",
                                    children: Uj(t.lastRotatedAt),
                                  }),
                                ],
                              }),
                              i.jsxs("div", {
                                className: "flex gap-2",
                                children: [
                                  i.jsx("dt", {
                                    className:
                                      "w-40 shrink-0 font-medium text-gray-500",
                                    children: e.trustKeys.fieldDaysActive,
                                  }),
                                  i.jsx("dd", {
                                    className: "text-gray-900",
                                    children:
                                      t.daysSinceRotation !== null
                                        ? `${t.daysSinceRotation} ${e.trustKeys.fieldDaysActiveSuffix}`
                                        : "—",
                                  }),
                                ],
                              }),
                            ],
                          }),
                        ],
                      }),
                      t.retiredGraceKids.length > 0 &&
                        i.jsxs("div", {
                          className:
                            "rounded-lg border border-gray-200 bg-white shadow-sm",
                          children: [
                            i.jsx("div", {
                              className: "border-b border-gray-100 px-5 py-3",
                              children: i.jsx("h3", {
                                className:
                                  "text-sm font-semibold text-gray-900",
                                children: e.trustKeys.retiredKeysTitle,
                              }),
                            }),
                            i.jsxs("table", {
                              className: "min-w-full text-sm",
                              "aria-label": e.trustKeys.retiredKeysAriaLabel,
                              children: [
                                i.jsx("thead", {
                                  children: i.jsx("tr", {
                                    className:
                                      "text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                                    children: i.jsx("th", {
                                      className: "px-5 pb-2 pt-3",
                                      children:
                                        e.trustKeys.retiredColIdentifier,
                                    }),
                                  }),
                                }),
                                i.jsx("tbody", {
                                  className: "divide-y divide-gray-100",
                                  children: t.retiredGraceKids.map((l) =>
                                    i.jsx(
                                      "tr",
                                      {
                                        className: "hover:bg-gray-50",
                                        children: i.jsx("td", {
                                          className:
                                            "px-5 py-2 font-mono text-gray-700",
                                          children: l,
                                        }),
                                      },
                                      l
                                    )
                                  ),
                                }),
                              ],
                            }),
                          ],
                        }),
                    ],
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    qj = (() => {
      try {
        const e = window;
        return (
          e.__AMCP_CONFIG__?.locale ??
          e.__AMCP_PS_CONFIG__?.locale ??
          navigator.language ??
          "en"
        )
          .split(/[-_]/)[0]
          .toLowerCase() === "es"
          ? "es-ES"
          : "en-GB";
      } catch {
        return "en-GB";
      }
    })(),
    Vj = new Intl.DateTimeFormat(qj, {
      dateStyle: "short",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    Kj = (e) => {
      try {
        return Vj.format(new Date(e));
      } catch {
        return e;
      }
    },
    Wj = {
      portal: "bg-gray-100 text-gray-700",
      "shopify-embed": "bg-green-50 text-green-700",
      "wp-embed": "bg-blue-50 text-blue-700",
      api: "bg-yellow-50 text-yellow-700",
      mcp: "bg-purple-50 text-purple-700",
      webhook: "bg-orange-50 text-orange-700",
    },
    Hj = ({ source: e, t }) => {
      const r = t.trustAuditLog.sourceLabels;
      return i.jsx("span", {
        className: `rounded-full px-2 py-0.5 text-xs font-medium ${Wj[e] ?? "bg-gray-100 text-gray-700"}`,
        children: r[e] ?? e,
      });
    },
    Qj = new Set(["ok", "success", "allow", "token_refresh_seamless"]),
    Gj = ({ outcome: e, t }) => {
      const r = Qj.has(e),
        n = t.trustAuditLog.outcomeLabels;
      return i.jsx("span", {
        className: `rounded-full px-2 py-0.5 text-xs font-medium ${r ? "bg-green-50 text-green-700" : "bg-red-50 text-red-700"}`,
        children: n[e] ?? e,
      });
    },
    Yj = () =>
      i.jsx("tr", {
        children: Array.from({ length: 6 }).map((e, t) =>
          i.jsx(
            "td",
            {
              className: "px-4 py-2",
              children: i.jsx("div", {
                className: "h-4 animate-pulse rounded bg-gray-100",
              }),
            },
            t
          )
        ),
      }),
    Jj = (e, t, r, n, s) => {
      const a = new URLSearchParams();
      return (
        a.set("limit", "20"),
        e && a.set("agentId", e),
        t !== "todos" && a.set("source", t),
        r && a.set("from", new Date(r).toISOString()),
        n && a.set("to", new Date(n).toISOString()),
        s && a.set("cursor", s),
        `/v1/embed/trust/audit?${a.toString()}`
      );
    },
    Xj = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              [t, r] = R.useState(""),
              [n, s] = R.useState("todos"),
              [a, o] = R.useState(""),
              [l, c] = R.useState(""),
              [u, d] = R.useState(null),
              [f, m] = R.useState([]),
              v = Jj(t, n, a, l, u),
              {
                data: x,
                isLoading: b,
                isError: j,
                error: g,
              } = _e({
                queryKey: ["trust-audit", t, n, a, l, u],
                queryFn: () => ae.get(v, Ph),
                placeholderData: (N) => N,
              }),
              p = R.useCallback((N, T) => {
                (N(T), d(null), m([]));
              }, []),
              y = R.useCallback((N) => {
                (s(N), d(null), m([]));
              }, []);
            pe.useEffect(() => {
              x?.items &&
                m(
                  u === null
                    ? x.items
                    : (N) => {
                        const T = new Set(N.map((O) => O.id)),
                          _ = x.items.filter((O) => !T.has(O.id));
                        return [...N, ..._];
                      }
                );
            }, [x, u]);
            const w = () => {
                x?.nextCursor && d(x.nextCursor);
              },
              S = f.length > 0 ? f : (x?.items ?? []),
              A = x?.chainIntegrity;
            return i.jsxs("div", {
              className: "p-6 max-w-5xl",
              children: [
                i.jsx("div", {
                  className:
                    "mb-5 rounded-lg border border-amber-100 bg-amber-50 px-5 py-4",
                  children: i.jsx("p", {
                    className: "text-sm leading-relaxed text-amber-800",
                    children: e.trustAuditLog.bookDescription,
                  }),
                }),
                A &&
                  i.jsxs("div", {
                    className: `mb-4 flex items-center gap-2 rounded border px-4 py-2 text-sm ${A.verified ? "border-green-300 bg-green-50 text-green-800" : "border-red-300 bg-red-50 text-red-700"}`,
                    role: "status",
                    children: [
                      i.jsx("span", {
                        className: `inline-block h-2 w-2 rounded-full ${A.verified ? "bg-green-500" : "bg-red-500"}`,
                      }),
                      A.verified
                        ? e.trustAuditLog.integrityVerified
                        : e.trustAuditLog.integrityError,
                      A.note &&
                        i.jsxs("span", {
                          className: "ml-2 text-xs opacity-70",
                          children: ["(", A.note, ")"],
                        }),
                    ],
                  }),
                i.jsxs("div", {
                  className: "mb-4 flex flex-wrap gap-3",
                  children: [
                    i.jsx("input", {
                      type: "text",
                      placeholder: e.trustAuditLog.filterAgentId,
                      value: t,
                      onChange: (N) => p(r, N.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": e.trustAuditLog.filterAgentIdAria,
                    }),
                    i.jsxs("select", {
                      value: n,
                      onChange: (N) => y(N.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": e.trustAuditLog.filterSourceAria,
                      children: [
                        i.jsx("option", {
                          value: "todos",
                          children: e.trustAuditLog.filterSource,
                        }),
                        i.jsx("option", {
                          value: "portal",
                          children: e.trustAuditLog.filterSourcePortal,
                        }),
                        i.jsx("option", {
                          value: "shopify-embed",
                          children: e.trustAuditLog.filterSourceShopify,
                        }),
                        i.jsx("option", {
                          value: "wp-embed",
                          children: e.trustAuditLog.filterSourceWp,
                        }),
                        i.jsx("option", {
                          value: "api",
                          children: e.trustAuditLog.filterSourceApi,
                        }),
                        i.jsx("option", {
                          value: "mcp",
                          children: e.trustAuditLog.filterSourceMcp,
                        }),
                        i.jsx("option", {
                          value: "webhook",
                          children: e.trustAuditLog.filterSourceWebhook,
                        }),
                      ],
                    }),
                    i.jsx("input", {
                      type: "date",
                      value: a,
                      onChange: (N) => p(o, N.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": e.trustAuditLog.filterDateFrom,
                    }),
                    i.jsx("input", {
                      type: "date",
                      value: l,
                      onChange: (N) => p(c, N.target.value),
                      className:
                        "rounded border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500",
                      "aria-label": e.trustAuditLog.filterDateTo,
                    }),
                  ],
                }),
                j &&
                  i.jsxs("div", {
                    className:
                      "mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700",
                    role: "alert",
                    children: [
                      e.trustAuditLog.errorLoading,
                      " ",
                      g instanceof Error ? g.message : e.common.errorUnknown,
                    ],
                  }),
                i.jsx("div", {
                  className:
                    "overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm",
                  children: i.jsxs("table", {
                    className: "min-w-full divide-y divide-gray-200 text-sm",
                    "aria-label": e.trustAuditLog.tableLabel,
                    children: [
                      i.jsx("thead", {
                        children: i.jsxs("tr", {
                          className:
                            "text-left text-xs font-medium uppercase tracking-wide text-gray-500",
                          children: [
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.trustAuditLog.colDate,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.trustAuditLog.colSource,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.trustAuditLog.colBucket,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.trustAuditLog.colTool,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.trustAuditLog.colOutcome,
                            }),
                            i.jsx("th", {
                              className: "px-4 pb-3 pt-4",
                              children: e.trustAuditLog.colAgentId,
                            }),
                          ],
                        }),
                      }),
                      i.jsxs("tbody", {
                        className: "divide-y divide-gray-100",
                        children: [
                          b &&
                            Array.from({ length: 5 }).map((N, T) =>
                              i.jsx(Yj, {}, T)
                            ),
                          !b &&
                            S.map((N) =>
                              i.jsxs(
                                "tr",
                                {
                                  className:
                                    "hover:bg-gray-50 transition-colors",
                                  children: [
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-500",
                                      children: Kj(N.createdAt),
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2",
                                      children: i.jsx(Hj, {
                                        source: N.source,
                                        t: e,
                                      }),
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-700",
                                      children: N.bucket ?? "—",
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-700",
                                      children: N.tool ?? "—",
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2",
                                      children: i.jsx(Gj, {
                                        outcome: N.outcome,
                                        t: e,
                                      }),
                                    }),
                                    i.jsx("td", {
                                      className: "px-4 py-2 text-gray-700",
                                      children: N.agentId ?? "—",
                                    }),
                                  ],
                                },
                                N.id
                              )
                            ),
                          !b &&
                            S.length === 0 &&
                            !j &&
                            i.jsx("tr", {
                              children: i.jsx("td", {
                                colSpan: 6,
                                className:
                                  "px-4 py-6 text-center text-sm text-gray-500",
                                children: e.trustAuditLog.noEntries,
                              }),
                            }),
                        ],
                      }),
                    ],
                  }),
                }),
                x?.nextCursor &&
                  i.jsx("div", {
                    className: "mt-4 flex justify-center",
                    children: i.jsx("button", {
                      onClick: w,
                      className:
                        "rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500",
                      children: e.trustAuditLog.loadMore,
                    }),
                  }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    ),
    eN = W({ activated: _t(), kid: oe() }),
    sd = (() => {
      try {
        const e = window;
        return (
          e.__AMCP_CONFIG__?.locale ??
          e.__AMCP_PS_CONFIG__?.locale ??
          navigator.language ??
          "en"
        )
          .split(/[-_]/)[0]
          .toLowerCase() === "es"
          ? "es-ES"
          : "en-GB";
      } catch {
        return "en-GB";
      }
    })(),
    tN = new Intl.DateTimeFormat(sd, {
      dateStyle: "long",
      timeStyle: "short",
      timeZone: "UTC",
    }),
    rN = (e, t) =>
      e == null
        ? {
            emoji: "⏳",
            color: "#8c9196",
            bg: "#f6f6f7",
            label: t.trustReceipts.scoreComputing,
          }
        : e >= 80
          ? {
              emoji: "🌟",
              color: "#1a7f4f",
              bg: "#e3f5ec",
              label: t.trustReceipts.scoreExcellent,
            }
          : e >= 50
            ? {
                emoji: "👍",
                color: "#b27400",
                bg: "#fff8ec",
                label: t.trustReceipts.scoreImprovable,
              }
            : {
                emoji: "⚠️",
                color: "#c0392b",
                bg: "#fff0ee",
                label: t.trustReceipts.scoreNeedsAttention,
              },
    ly = ({ name: e, value: t, weight: r, dataQuality: n, t: s }) => {
      const o = s.trustReceipts.subDim[e] ?? e.replace(/_/g, " "),
        l = n === "not_applicable" || t == null,
        c = l ? null : Math.min(100, Math.max(0, t)),
        u = l ? "⏳" : (c ?? 0) >= 80 ? "✅" : (c ?? 0) >= 50 ? "🟡" : "🔴",
        d = l
          ? "#8c9196"
          : (c ?? 0) >= 80
            ? "#1a7f4f"
            : (c ?? 0) >= 50
              ? "#b27400"
              : "#c0392b";
      return i.jsxs("div", {
        className:
          "flex items-center gap-2 border-b border-gray-50 py-1 text-xs",
        children: [
          i.jsx("span", { className: "shrink-0 text-sm", children: u }),
          i.jsx("span", { className: "flex-1 text-gray-600", children: o }),
          i.jsx("span", {
            className: "shrink-0 text-gray-400",
            children: s.trustReceipts.weightLabel.replace(
              "{{pct}}",
              String(Math.round(r * 100))
            ),
          }),
          i.jsx("span", {
            className: "min-w-[52px] shrink-0 text-right font-bold",
            style: { color: d },
            children: l ? s.trustReceipts.noDataLabel : `${c}/100`,
          }),
        ],
      });
    },
    nN = ({ name: e, dim: t, confidenceLevel: r, t: n }) => {
      const s = {
          merchantReliabilityBase: n.trustReceipts.dimMerchantReliability,
          agenticReadiness: n.trustReceipts.dimAgenticReadiness,
          agenticEvidence: n.trustReceipts.dimAgenticEvidence,
        },
        a = {
          merchantReliabilityBase: {
            cold_start: n.trustReceipts.unlockMerchantColdStart,
          },
          agenticEvidence: {
            cold_start: n.trustReceipts.unlockAgentColdStart,
            human_established: n.trustReceipts.unlockAgentHuman,
          },
        },
        o = t.dataQuality === "not_applicable",
        l = o ? 0 : Math.min(100, Math.max(0, t.value ?? 0)),
        c = s[e] ?? e.replace(/_/g, " "),
        u = l >= 80 ? "#007f5f" : l >= 50 ? "#ffc453" : "#d72c0d",
        d = t.subcomponents ? Object.entries(t.subcomponents) : [],
        f = a[e]?.[r] ?? n.trustReceipts.unlockAreaDefault;
      return i.jsxs("div", {
        className:
          "mb-5 border-b border-gray-100 pb-5 last:border-0 last:mb-0 last:pb-0",
        children: [
          i.jsxs("div", {
            className: "mb-2 flex items-center justify-between",
            children: [
              i.jsx("span", {
                className: "text-sm font-semibold",
                children: c,
              }),
              i.jsxs("div", {
                className: "flex items-center gap-2",
                children: [
                  !o &&
                    i.jsx("span", {
                      className: "text-xs text-gray-400",
                      children: n.trustReceipts.weightLabel.replace(
                        "{{pct}}",
                        String(Math.round(t.weight * 100))
                      ),
                    }),
                  i.jsx("span", {
                    className: "text-sm font-bold",
                    style: {
                      color: o
                        ? "#8c9196"
                        : l >= 80
                          ? "#1a7f4f"
                          : l >= 50
                            ? "#b27400"
                            : "#c0392b",
                    },
                    children: o ? n.trustReceipts.notApplicable : `${l}/100`,
                  }),
                ],
              }),
            ],
          }),
          o
            ? i.jsxs("div", {
                children: [
                  i.jsx("div", {
                    className:
                      "mb-2 rounded border border-dashed border-gray-300 bg-gray-50 px-3 py-2 text-xs text-gray-500",
                    children: f,
                  }),
                  d.length > 0 &&
                    i.jsxs("div", {
                      className:
                        "rounded border border-gray-200 bg-gray-50 px-3 py-2 opacity-50",
                      children: [
                        i.jsx("div", {
                          className:
                            "mb-1 text-xs font-bold uppercase tracking-wide text-gray-400",
                          children: n.trustReceipts.countWillActivate,
                        }),
                        d.map(([m, v]) =>
                          i.jsx(
                            ly,
                            {
                              name: m,
                              value: null,
                              weight: v.weight,
                              dataQuality: "not_applicable",
                              t: n,
                            },
                            m
                          )
                        ),
                      ],
                    }),
                ],
              })
            : i.jsxs(i.Fragment, {
                children: [
                  i.jsx("div", {
                    className: "mb-2 h-2 overflow-hidden rounded bg-gray-200",
                    children: i.jsx("div", {
                      className: "h-full transition-all",
                      style: { width: `${l}%`, background: u },
                    }),
                  }),
                  t.recommendations.length > 0 &&
                    i.jsx("div", {
                      className: "mb-2 rounded px-3 py-2 text-xs text-gray-700",
                      style: {
                        background:
                          l < 50 ? "#fff4f4" : l < 75 ? "#fff8e1" : "#f6f6f7",
                      },
                      children:
                        n.trustReceipts.recText[
                          t.recommendations[0].replace(/^rec\./, "")
                        ] ?? t.recommendations[0],
                    }),
                  d.length > 0 &&
                    i.jsxs("div", {
                      className:
                        "rounded border border-gray-200 bg-gray-50 px-3 py-2",
                      children: [
                        i.jsx("div", {
                          className:
                            "mb-1 text-xs font-bold uppercase tracking-wide text-gray-400",
                          children: n.trustReceipts.measuredNow,
                        }),
                        d.map(([m, v]) =>
                          i.jsx(
                            ly,
                            {
                              name: m,
                              value: v.value,
                              weight: v.weight,
                              dataQuality: v.dataQuality,
                              t: n,
                            },
                            m
                          )
                        ),
                      ],
                    }),
                ],
              }),
        ],
      });
    },
    sN = Object.freeze(
      Object.defineProperty(
        {
          __proto__: null,
          default: () => {
            const e = Re(),
              t = Wb(),
              r = {
                insufficient_data: {
                  label: e.trustReceipts.confidenceInsufficient,
                  color: "#8c9196",
                  bg: "#f6f6f7",
                  border: "#d5d8dc",
                },
                cold_start: {
                  label: e.trustReceipts.confidenceColdStart,
                  color: "#8c9196",
                  bg: "#f6f6f7",
                  border: "#d5d8dc",
                },
                human_established: {
                  label: e.trustReceipts.confidenceHumanEstablished,
                  color: "#b27400",
                  bg: "#fff8ec",
                  border: "#ffc453",
                },
                agent_verified_thin: {
                  label: e.trustReceipts.confidenceAgentThin,
                  color: "#1a7f4f",
                  bg: "#e3f5ec",
                  border: "#95c9a8",
                },
                agent_verified: {
                  label: e.trustReceipts.confidenceAgentVerified,
                  color: "#1a7f4f",
                  bg: "#e3f5ec",
                  border: "#95c9a8",
                },
              },
              {
                data: n,
                isLoading: s,
                isError: a,
                error: o,
              } = _e({
                queryKey: ["trust-overview"],
                queryFn: () => ae.get("/v1/embed/trust/overview", Oh),
              }),
              l = fr(),
              c = Pt({
                mutationFn: () =>
                  ae.post("/v1/embed/trust/keys/activate", {}, eN),
                onSuccess: () => {
                  (l.invalidateQueries({ queryKey: ["trust-keys-inicio"] }),
                    l.invalidateQueries({ queryKey: ["trust-overview"] }));
                },
              }),
              { data: u } = _e({
                queryKey: ["trust-keys-inicio"],
                queryFn: async () => {
                  try {
                    return await ae.get("/v1/embed/trust/keys", Fa);
                  } catch (Z) {
                    if (Z instanceof Bt && Z.status === 404) return null;
                    throw Z;
                  }
                },
                retry: !1,
              });
            if (s)
              return i.jsx("div", {
                className: "p-6 space-y-3",
                children: Array.from({ length: 3 }).map((Z, Oe) =>
                  i.jsx(
                    "div",
                    { className: "h-24 animate-pulse rounded-lg bg-gray-100" },
                    Oe
                  )
                ),
              });
            if (a)
              return i.jsx("div", {
                className: "p-6",
                children: i.jsxs("div", {
                  className:
                    "rounded border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800",
                  role: "alert",
                  children: [
                    e.trustReceipts.errorLoadingData,
                    " ",
                    o instanceof Error ? o.message : e.common.errorUnknown,
                  ],
                }),
              });
            if (!n)
              return i.jsx("div", {
                className: "p-6 text-sm text-gray-500",
                children: e.trustReceipts.noData,
              });
            const { score: d, complianceSummary: f, computedAt: m } = n,
              v = d.status === "ready" || d.status === "stale",
              x = v ? d.score : null,
              b = v ? d.confidenceLevel : "cold_start",
              j = v
                ? d.evidenceProfile
                : { humanOrders: 0, agentOrders: 0, storeAgeDays: 0 },
              g = v ? d.scoreCap : 100,
              p = v ? d.nextMilestone : null,
              y = v ? d.breakdown : {},
              w = rN(x, e),
              S = r[b] ?? r.cold_start,
              A = y.agenticReadiness?.recommendations ?? [],
              N = A.includes("rec.activate_security_key"),
              T = A.includes("rec.add_return_policy"),
              _ = A.includes("rec.add_shipping_policy"),
              O = f.overallStatus !== "ok" || f.pendingActionsCount > 0,
              q = [
                {
                  id: "sello",
                  label: e.trustReceipts.checklistSello,
                  detail: e.trustReceipts.checklistSelloDetail,
                  done: !N,
                  action: {
                    label: e.trustReceipts.goToKeys,
                    onClick: () => c.mutate(),
                  },
                },
                {
                  id: "compliance",
                  label: e.trustReceipts.checklistCompliance,
                  detail: e.trustReceipts.checklistComplianceDetail,
                  done: !O,
                  action: {
                    label: e.trustReceipts.checklistComplianceAction,
                    href: "admin.php?page=wc-settings&tab=checkout",
                  },
                },
                {
                  id: "devoluciones",
                  label: e.trustReceipts.checklistDevoluciones,
                  detail: e.trustReceipts.checklistDevolucionesDetail,
                  done: !T,
                  action: {
                    label: e.trustReceipts.checklistDevolucionesAction,
                    href: "admin.php?page=wc-settings&tab=account",
                  },
                },
                {
                  id: "envio",
                  label: e.trustReceipts.checklistEnvio,
                  detail: e.trustReceipts.checklistEnvioDetail,
                  done: !_,
                  action: {
                    label: e.trustReceipts.checklistEnvioAction,
                    href: "admin.php?page=wc-settings&tab=shipping",
                  },
                },
              ],
              ee = q.filter((Z) => Z.done).length;
            return i.jsxs("div", {
              className: "p-6 max-w-4xl space-y-6",
              children: [
                i.jsxs("div", {
                  className:
                    "rounded-lg border border-gray-200 bg-white p-6 shadow-sm",
                  children: [
                    i.jsx("div", {
                      className: "mb-4",
                      children: i.jsx("h2", {
                        className: "text-base font-semibold text-gray-900",
                        children: e.trustReceipts.panelTitle,
                      }),
                    }),
                    i.jsxs("div", {
                      className: "flex flex-wrap items-center gap-6",
                      children: [
                        i.jsxs("div", {
                          className:
                            "flex h-24 w-24 shrink-0 flex-col items-center justify-center rounded-full",
                          style: {
                            border: `5px solid ${w.color}`,
                            background: w.bg,
                          },
                          children: [
                            i.jsx("span", {
                              className: "text-3xl font-extrabold leading-none",
                              style: { color: w.color },
                              children: x ?? "—",
                            }),
                            x != null &&
                              i.jsx("span", {
                                className: "text-xs font-semibold",
                                style: { color: w.color },
                                children: "/ 100",
                              }),
                          ],
                        }),
                        i.jsxs("div", {
                          className: "flex-1 min-w-0",
                          children: [
                            i.jsxs("div", {
                              className:
                                "mb-2 flex flex-wrap items-center gap-2",
                              children: [
                                i.jsx("span", {
                                  className: "text-xl",
                                  children: w.emoji,
                                }),
                                i.jsx("span", {
                                  className: "text-lg font-bold",
                                  style: { color: w.color },
                                  children: w.label,
                                }),
                                i.jsx("span", {
                                  className:
                                    "rounded-full px-3 py-0.5 text-xs font-bold",
                                  style: {
                                    background: S.bg,
                                    color: S.color,
                                    border: `1px solid ${S.border}`,
                                  },
                                  children: S.label,
                                }),
                              ],
                            }),
                            i.jsx("p", {
                              className: "text-sm text-gray-500",
                              children:
                                b === "cold_start"
                                  ? e.trustReceipts.coldStartDesc
                                  : b === "human_established"
                                    ? e.trustReceipts.humanEstablishedDesc
                                    : (x ?? 0) >= 90
                                      ? e.trustReceipts.scoreHighDesc
                                      : (x ?? 0) >= 70
                                        ? e.trustReceipts.scoreMidDesc
                                        : e.trustReceipts.scoreLowDesc,
                            }),
                            i.jsx("p", {
                              className: "mt-1 text-xs text-gray-400",
                              children: e.trustReceipts.computedAt.replace(
                                "{{date}}",
                                tN.format(new Date(m))
                              ),
                            }),
                          ],
                        }),
                        f.pendingActionsCount > 0 &&
                          i.jsxs("div", {
                            className:
                              "shrink-0 rounded-lg border border-red-200 bg-red-50 px-5 py-3 text-center",
                            children: [
                              i.jsx("div", {
                                className:
                                  "text-2xl font-extrabold text-red-700",
                                children: f.pendingActionsCount,
                              }),
                              i.jsx("div", {
                                className: "text-xs font-semibold text-red-700",
                                children:
                                  f.pendingActionsCount === 1
                                    ? e.trustReceipts.pendingActionsOne
                                    : e.trustReceipts.pendingActionsMany,
                              }),
                            ],
                          }),
                      ],
                    }),
                    i.jsxs("div", {
                      className: "mt-4",
                      children: [
                        i.jsxs("div", {
                          className: "relative h-3 rounded-full",
                          style: {
                            background:
                              "linear-gradient(to right, #fac9c3 0%, #ffd79d 40%, #95c9a8 70%, #1a7f4f 100%)",
                          },
                          children: [
                            i.jsx("div", {
                              className:
                                "absolute top-1/2 h-5 w-5 -translate-y-1/2 rounded-full border-2 bg-white shadow",
                              style: {
                                left: `calc(${Math.min(x ?? 0, 99)}% - 10px)`,
                                borderColor: w.color,
                              },
                            }),
                            g < 100 &&
                              i.jsx("div", {
                                className:
                                  "absolute top-0 bottom-0 w-0.5 rounded bg-gray-400",
                                style: { left: `${g}%` },
                                title: e.trustReceipts.scoreCurrent.replace(
                                  "{{cap}}",
                                  String(g)
                                ),
                              }),
                          ],
                        }),
                        i.jsxs("div", {
                          className:
                            "mt-1 flex justify-between text-xs text-gray-400",
                          children: [
                            i.jsx("span", {
                              children: e.trustReceipts.scoreLow,
                            }),
                            i.jsx("span", {
                              children: e.trustReceipts.scoreMid,
                            }),
                            g < 100
                              ? i.jsx("span", {
                                  style: { color: S.color },
                                  children: e.trustReceipts.scoreCap.replace(
                                    "{{cap}}",
                                    String(g)
                                  ),
                                })
                              : i.jsx("span", {
                                  children: e.trustReceipts.scoreHigh,
                                }),
                          ],
                        }),
                      ],
                    }),
                    i.jsxs("div", {
                      className: "mt-4",
                      style: {
                        background: "#f0f9ff",
                        border: "1px solid #bae6fd",
                        borderRadius: "8px",
                        padding: "10px 14px",
                        fontSize: "13px",
                        color: "#0369a1",
                        display: "flex",
                        alignItems: "center",
                        gap: "8px",
                      },
                      children: [
                        i.jsx("span", {
                          style: { fontSize: "16px" },
                          children: "ℹ️",
                        }),
                        i.jsxs("a", {
                          href: `https://trusteed.xyz/${t}/for-merchants/trusted-score`,
                          target: "_blank",
                          rel: "noopener noreferrer",
                          style: { color: "#0369a1", textDecoration: "none" },
                          children: [e.trustReceipts.scoringMethodLink, " →"],
                        }),
                      ],
                    }),
                    p &&
                      i.jsx("div", {
                        className:
                          "mt-4 flex items-center gap-2 rounded-lg px-4 py-3 text-sm",
                        style: {
                          background: S.bg,
                          border: `1px solid ${S.border}`,
                          color: S.color,
                        },
                        children: i.jsx("span", {
                          children:
                            e.trustReceipts.milestoneText[
                              p.replace(/^milestone\./, "")
                            ] ?? p,
                        }),
                      }),
                    (j.storeAgeDays > 0 ||
                      j.humanOrders > 0 ||
                      j.agentOrders > 0) &&
                      i.jsxs("div", {
                        className: "mt-4 flex flex-wrap gap-2",
                        children: [
                          j.storeAgeDays > 0 &&
                            i.jsxs("div", {
                              className:
                                "rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600",
                              children: [
                                e.trustReceipts.storeAgeLine,
                                " ",
                                i.jsx("strong", {
                                  className: "text-gray-900",
                                  children:
                                    j.storeAgeDays >= 365
                                      ? `${Math.floor(j.storeAgeDays / 365)} ${Math.floor(j.storeAgeDays / 365) !== 1 ? e.trustReceipts.storeYears : e.trustReceipts.storeYear}`
                                      : `${j.storeAgeDays} ${e.trustReceipts.storeDays}`,
                                }),
                                " ",
                                e.trustReceipts.storeActive,
                              ],
                            }),
                          j.humanOrders > 0 &&
                            i.jsxs("div", {
                              className:
                                "rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600",
                              children: [
                                i.jsxs("strong", {
                                  className: "text-gray-900",
                                  children: [
                                    j.humanOrders.toLocaleString(sd),
                                    " ",
                                    j.humanOrders !== 1
                                      ? e.trustReceipts.humanOrders + "s"
                                      : e.trustReceipts.humanOrders,
                                  ],
                                }),
                                " ",
                                e.trustReceipts.humanOrdersOf,
                              ],
                            }),
                          j.agentOrders > 0 &&
                            i.jsxs("div", {
                              className:
                                "rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700",
                              children: [
                                i.jsxs("strong", {
                                  children: [
                                    j.agentOrders.toLocaleString(sd),
                                    " ",
                                    j.agentOrders !== 1
                                      ? e.trustReceipts.agentSalePlural
                                      : e.trustReceipts.agentSale,
                                  ],
                                }),
                                " ",
                                e.trustReceipts.agentSalesOf,
                              ],
                            }),
                        ],
                      }),
                  ],
                }),
                v &&
                  Object.keys(y).length > 0 &&
                  i.jsxs("div", {
                    className:
                      "rounded-lg border border-gray-200 bg-white p-6 shadow-sm",
                    children: [
                      i.jsx("h2", {
                        className: "mb-4 text-base font-semibold text-gray-900",
                        children: e.trustReceipts.breakdownTitle,
                      }),
                      b === "cold_start" &&
                        i.jsx("div", {
                          className:
                            "mb-4 rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800",
                          children: e.trustReceipts.coldStartNotice,
                        }),
                      Object.entries(y).map(([Z, Oe]) =>
                        i.jsx(
                          nN,
                          { name: Z, dim: Oe, confidenceLevel: b, t: e },
                          Z
                        )
                      ),
                    ],
                  }),
                i.jsxs("div", {
                  className:
                    "rounded-lg border border-gray-200 bg-white p-6 shadow-sm",
                  children: [
                    i.jsx("h2", {
                      className: "mb-4 text-base font-semibold text-gray-900",
                      children: e.trustReceipts.checklistTitle,
                    }),
                    i.jsx("div", {
                      className:
                        "mb-5 flex items-center gap-3 rounded-lg px-4 py-3",
                      style: {
                        background: ee === q.length ? "#e3f5ec" : "#f6f6f7",
                        border: `1px solid ${ee === q.length ? "#95c9a8" : "#e4e5e7"}`,
                      },
                      children: i.jsxs("div", {
                        children: [
                          i.jsx("span", {
                            className: "font-bold",
                            children: e.trustReceipts.checklistProgress
                              .replace("{{done}}", String(ee))
                              .replace("{{total}}", String(q.length)),
                          }),
                          ee < q.length &&
                            i.jsx("span", {
                              className: "ml-2 text-sm text-gray-500",
                              children:
                                q.length - ee > 1
                                  ? e.trustReceipts.checklistMissingMany.replace(
                                      "{{count}}",
                                      String(q.length - ee)
                                    )
                                  : e.trustReceipts.checklistMissingOne.replace(
                                      "{{count}}",
                                      String(q.length - ee)
                                    ),
                            }),
                          ee === q.length &&
                            i.jsx("span", {
                              className: "ml-2 text-sm text-green-700",
                              children: e.trustReceipts.checklistAllDone,
                            }),
                        ],
                      }),
                    }),
                    i.jsx("p", {
                      className:
                        "mb-3 text-xs font-bold uppercase tracking-wide text-gray-400",
                      children: e.trustReceipts.checklistNow,
                    }),
                    i.jsx("div", {
                      className: "space-y-4",
                      children: q.map((Z) =>
                        i.jsxs(
                          "div",
                          {
                            className: "flex items-start gap-3",
                            children: [
                              i.jsx("span", {
                                className:
                                  "mt-0.5 shrink-0 text-xl leading-none",
                                children: Z.done ? "✅" : "❌",
                              }),
                              i.jsxs("div", {
                                className: "flex-1",
                                children: [
                                  i.jsx("p", {
                                    className: "text-sm font-semibold",
                                    style: {
                                      color: Z.done ? "#1a7f4f" : "#202223",
                                      textDecoration: Z.done
                                        ? "line-through"
                                        : "none",
                                    },
                                    children: Z.label,
                                  }),
                                  !Z.done &&
                                    i.jsxs(i.Fragment, {
                                      children: [
                                        i.jsx("p", {
                                          className:
                                            "mt-1 text-xs text-gray-500 leading-relaxed",
                                          children: Z.detail,
                                        }),
                                        Z.action &&
                                          ("href" in Z.action && Z.action.href
                                            ? i.jsxs("a", {
                                                href: Z.action.href,
                                                className:
                                                  "mt-2 inline-block rounded border border-blue-500 px-3 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-50",
                                                children: [
                                                  Z.action.label,
                                                  " →",
                                                ],
                                              })
                                            : i.jsxs(i.Fragment, {
                                                children: [
                                                  i.jsx("button", {
                                                    onClick: Z.action.onClick,
                                                    disabled: c.isPending,
                                                    className:
                                                      "mt-2 inline-block rounded border border-blue-500 px-3 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-50 disabled:opacity-60",
                                                    children: c.isPending
                                                      ? e.common.loading
                                                      : `${Z.action.label} →`,
                                                  }),
                                                  c.isError &&
                                                    i.jsx("p", {
                                                      className:
                                                        "mt-2 text-xs text-red-600",
                                                      role: "alert",
                                                      children:
                                                        c.error instanceof Error
                                                          ? c.error.message
                                                          : e.common
                                                              .errorUnknown,
                                                    }),
                                                ],
                                              })),
                                      ],
                                    }),
                                ],
                              }),
                            ],
                          },
                          Z.id
                        )
                      ),
                    }),
                    i.jsx("p", {
                      className:
                        "mb-3 mt-6 text-xs font-bold uppercase tracking-wide text-gray-400",
                      children: e.trustReceipts.checklistAutoTitle,
                    }),
                    (() => {
                      const Z =
                          n?.reliabilitySignals?.fulfillmentOnTimeRate ?? null,
                        Oe = n?.reliabilitySignals?.disputeRate ?? null,
                        nr = [
                          {
                            id: "fulfillment",
                            label: e.trustReceipts.checklistFulfillment,
                            detail: e.trustReceipts.checklistFulfillmentDetail,
                            measured: Z != null,
                            value: Z != null ? `${Math.round(Z * 100)}%` : null,
                          },
                          {
                            id: "disputes",
                            label: e.trustReceipts.checklistDisputes,
                            detail: e.trustReceipts.checklistDisputesDetail,
                            measured: Oe != null,
                            value:
                              Oe != null ? `${(Oe * 100).toFixed(1)}%` : null,
                          },
                        ],
                        Ya = nr.some((gt) => gt.measured);
                      return i.jsxs(i.Fragment, {
                        children: [
                          i.jsx("div", {
                            className:
                              "rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-500",
                            children: Ya
                              ? e.trustReceipts.checklistAutoActiveNote
                              : e.trustReceipts.checklistAutoNote,
                          }),
                          nr.map((gt) =>
                            i.jsxs(
                              "div",
                              {
                                className: `mt-3 flex items-start gap-3${gt.measured ? "" : " opacity-55"}`,
                                children: [
                                  i.jsx("span", {
                                    className: "shrink-0 text-xl leading-none",
                                    children: gt.measured ? "✅" : "⏳",
                                  }),
                                  i.jsxs("div", {
                                    children: [
                                      i.jsxs("p", {
                                        className: `text-sm font-semibold ${gt.measured ? "text-gray-700" : "text-gray-500"}`,
                                        children: [
                                          gt.label,
                                          gt.value != null &&
                                            i.jsx("span", {
                                              className:
                                                "ml-2 font-normal text-emerald-600",
                                              children: gt.value,
                                            }),
                                        ],
                                      }),
                                      i.jsx("p", {
                                        className: "text-xs text-gray-400",
                                        children: gt.detail,
                                      }),
                                    ],
                                  }),
                                ],
                              },
                              gt.id
                            )
                          ),
                        ],
                      });
                    })(),
                  ],
                }),
                i.jsxs("div", {
                  className:
                    "rounded-lg border border-gray-200 bg-white p-6 shadow-sm",
                  children: [
                    i.jsx("h2", {
                      className: "mb-4 text-base font-semibold text-gray-900",
                      children: e.trustReceipts.practicesTitle,
                    }),
                    i.jsx("div", {
                      className: "space-y-3",
                      children: [
                        {
                          title: e.trustReceipts.practice1Title,
                          body: e.trustReceipts.practice1Body,
                        },
                        {
                          title: e.trustReceipts.practice2Title,
                          body: e.trustReceipts.practice2Body,
                        },
                        {
                          title: e.trustReceipts.practice3Title,
                          body: e.trustReceipts.practice3Body,
                        },
                      ].map((Z) =>
                        i.jsx(
                          "div",
                          {
                            className:
                              "flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3",
                            children: i.jsxs("div", {
                              children: [
                                i.jsx("p", {
                                  className:
                                    "text-sm font-semibold text-gray-900",
                                  children: Z.title,
                                }),
                                i.jsx("p", {
                                  className:
                                    "mt-1 text-xs text-gray-600 leading-relaxed",
                                  children: Z.body,
                                }),
                              ],
                            }),
                          },
                          Z.title
                        )
                      ),
                    }),
                  ],
                }),
                i.jsxs("div", {
                  className:
                    "rounded-lg border border-gray-200 bg-white p-6 shadow-sm",
                  children: [
                    i.jsx("h2", {
                      className: "mb-4 text-base font-semibold text-gray-900",
                      children: e.trustReceipts.faqTitle,
                    }),
                    i.jsx("div", {
                      className: "space-y-3",
                      children: [
                        { q: e.trustReceipts.faq1Q, a: e.trustReceipts.faq1A },
                        { q: e.trustReceipts.faq2Q, a: e.trustReceipts.faq2A },
                        { q: e.trustReceipts.faq3Q, a: e.trustReceipts.faq3A },
                      ].map((Z) =>
                        i.jsxs(
                          "div",
                          {
                            className:
                              "rounded-lg border border-gray-200 bg-gray-50 p-4",
                            children: [
                              i.jsx("p", {
                                className:
                                  "text-sm font-semibold text-gray-900",
                                children: Z.q,
                              }),
                              i.jsx("p", {
                                className:
                                  "mt-2 text-xs text-gray-600 leading-relaxed",
                                children: Z.a,
                              }),
                            ],
                          },
                          Z.q
                        )
                      ),
                    }),
                  ],
                }),
                i.jsxs("div", {
                  className:
                    "rounded-lg border border-gray-200 bg-white p-6 shadow-sm",
                  children: [
                    i.jsx("h2", {
                      className: "mb-3 text-base font-semibold text-gray-900",
                      children: e.trustReceipts.securityTitle,
                    }),
                    u
                      ? i.jsxs(i.Fragment, {
                          children: [
                            i.jsx("div", {
                              className: "flex items-center gap-3",
                              children: i.jsxs("p", {
                                className: "text-sm text-gray-700",
                                children: [
                                  i.jsx("strong", {
                                    children:
                                      e.trustReceipts.securityProtectionActive,
                                  }),
                                  " ",
                                  e.trustReceipts.securityActiveDesc,
                                ],
                              }),
                            }),
                            u.daysSinceRotation !== null &&
                              u.daysSinceRotation > 90 &&
                              i.jsx("div", {
                                className:
                                  "mt-3 rounded border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800",
                                children:
                                  e.trustReceipts.securityRotationWarning.replace(
                                    "{{days}}",
                                    String(u.daysSinceRotation)
                                  ),
                              }),
                          ],
                        })
                      : i.jsxs("div", {
                          children: [
                            i.jsx("div", {
                              className: "flex items-center gap-3",
                              children: i.jsxs("p", {
                                className: "text-sm text-gray-600",
                                children: [
                                  e.trustReceipts.securityNotActive,
                                  " ",
                                  i.jsx("button", {
                                    onClick: () => c.mutate(),
                                    disabled: c.isPending,
                                    className:
                                      "font-semibold text-blue-600 hover:underline disabled:opacity-60",
                                    children: c.isPending
                                      ? e.common.loading
                                      : e.trustReceipts.securityActivate,
                                  }),
                                ],
                              }),
                            }),
                            c.isError &&
                              i.jsx("p", {
                                className: "mt-2 text-xs text-red-600",
                                role: "alert",
                                children:
                                  c.error instanceof Error
                                    ? c.error.message
                                    : e.common.errorUnknown,
                              }),
                          ],
                        }),
                  ],
                }),
              ],
            });
          },
        },
        Symbol.toStringTag,
        { value: "Module" }
      )
    );
})();
