module.exports = {
  root: true,
  'globals': {
    'Stats': false,
    'barba': false,
    'gsap': false,
    'Expo': false,
    'Linear': false,
  },
  env: {
    browser: true,
    node: true,
    es2021: true,
  },
  parserOptions: {
    sourceType: 'module',
    ecmaVersion: 12,
  },
  extends: [
    'eslint:recommended',
    'prettier'
  ],
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off'
  },
}
