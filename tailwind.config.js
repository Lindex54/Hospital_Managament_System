module.exports = {
  content: [
    "./public/**/*.php",
    "./app/**/*.php",
    "./public/assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: "#eef8ff",
          100: "#d9edff",
          600: "#0f6cbd",
          700: "#0d5da4",
          900: "#12344d"
        }
      }
    }
  },
  plugins: []
};

