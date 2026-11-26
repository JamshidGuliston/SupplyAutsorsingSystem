module.exports = {
  preset: 'react-native',
  setupFiles: ['./jest.setup.js'],
  transformIgnorePatterns: [
    'node_modules/(?!(?:@react-native|react-native|@react-navigation|react-native-mmkv|react-native-svg|react-native-safe-area-context)/)',
  ],
};
