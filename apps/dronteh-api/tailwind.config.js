module.exports = {
  mode: "jit",
  content: [
    "./src/**/*.php",
    "./templates/**/*.{html,twig}",
    "./assets/js/*.{js,ts}",
  ],
  theme: {
    extend: {},
    container: {
      center: true,
    },
  },
  plugins: [],
};
