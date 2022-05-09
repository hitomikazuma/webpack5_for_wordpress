import 'core-js/stable'
import 'regenerator-runtime/runtime'

import { Debug } from './debug'
import { Transition } from './transition'

const updateMeta = (next) => {
  const { head } = document
  const targetHead = next.html.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0]
  const newPageHead = document.createElement('head')
  newPageHead.innerHTML = targetHead
  const removeHeadTags = [
    "meta[name='robots']",
    "meta[name='keywords']",
    "meta[name='description']",
    "meta[property='og:title']",
    "meta[property='og:type']",
    "meta[property='og:url']",
    "meta[property='og:image']",
    "meta[property='og:image:width']",
    "meta[property='og:image:height']",
    "meta[property='og:description']",
    "meta[property='og:site_name']",
    "link[rel='alternate']",
    "link[rel='canonical']",
    "link[rel='next']",
    "link[rel='prev']",
  ].join(',')
  const headTags = [...head.querySelectorAll(removeHeadTags)]
  const newHeadTags = [...newPageHead.querySelectorAll(removeHeadTags)]
  headTags.forEach((item) => head.removeChild(item))
  newHeadTags.forEach((item) => head.appendChild(item))

  // ga
  // if (typeof ga === 'function') ga('send', 'pageview', window.location.pathname) // eslint-disable-line no-undef
  // if (typeof gtag === 'function') {
  //   gtag('config', '******', { page_path: window.location.pathname }) // eslint-disable-line no-undef
  // }
}

// new Debug()
const transition = new Transition()

barba.hooks.before(() => {
  document.querySelector('html').classList.add('is-transitioning')
  barba.wrapper.classList.add('is-animating')
})
barba.hooks.after(() => {
  document.querySelector('html').classList.remove('is-transitioning')
  barba.wrapper.classList.remove('is-animating')
})
barba.hooks.enter(() => {
  window.scrollTo(0, 0)
})

barba.init({
  sync: true,
  views: [
    {
      namespace: 'top',
      beforeEnter() {},
      afterLeave() {},
    },
  ],
  transitions: [
    {
      async once() {},
      async leave() {
        const done = this.async()
        transition.beforeAnimate()
        await transition.delay(250)
        done()
      },
      beforeEnter({ next }) {
        updateMeta(next)
      },
      enter() {
        transition.afterAnimate()
      },
      afterLeave() {},
    },
  ],
})
