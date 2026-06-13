
// Let's trace the stack
Error.stackTraceLimit = Infinity;

try {
  // Try to require what mix does
  require('./node_modules/laravel-mix/setup/webpack.config.js');
} catch (err) {
  console.error('Error:', err.message);
  console.error('Stack:', err.stack);
}
