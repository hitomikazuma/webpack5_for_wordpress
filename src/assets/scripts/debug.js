export class Debug {
  constructor() {
    const stats = new Stats()
    stats.showPanel(0)
    Object.assign(stats.domElement.style, {
      position: 'fixed',
      zIndex: '999999999',
      top: '0',
      right: '0',
      height: 'max-content',
    })
    document.body.appendChild(stats.domElement)

    const stepFrame = () => {
      stats.begin()
      stats.end()
      requestAnimationFrame(() => stepFrame())
    }
    stepFrame()
  }
}
