module.exports = (api) => {
  // Cache the configuration based on the environment
  api.cache.using(() => process.env.NODE_ENV);

  const presets = [
    [
      '@vue/app',
      {
        useBuiltIns: 'entry',
        corejs: 3,
      },
    ],
  ];

  const plugins = [
    '@babel/plugin-syntax-dynamic-import',
    '@babel/plugin-proposal-object-rest-spread',
    '@babel/plugin-proposal-optional-chaining',
    '@babel/plugin-proposal-nullish-coalescing-operator',
    '@babel/plugin-transform-modules-commonjs',
    ['@babel/plugin-transform-runtime', {
      corejs: false,
      helpers: true,
      regenerator: true,
      useESModules: false
    }],
    // Add any additional plugins here
  ];

  return {
    presets,
    plugins,
    sourceType: 'unambiguous',
    assumptions: {
      setPublicClassFields: true,
      privateFieldsAsProperties: true
    }
  };
};
