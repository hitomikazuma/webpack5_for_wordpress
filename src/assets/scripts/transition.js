export class Transition {
  constructor() {}

  delay(second) {
    let sec = second
    sec = second || 1000
    return new Promise((done) => {
      setTimeout(() => {
        done()
      }, sec)
    })
  }

  beforeAnimate() {
    gsap.fromTo(
      'body',
      {
        opacity: 1,
      },
      {
        duration: 0.2,
        ease: Expo.easeOut,
        opacity: 0,
      },
    )
  }

  afterAnimate() {
    gsap.to('body', {
      duration: 0.2,
      ease: Expo.easeOut,
      opacity: 1,
    })
  }
}
