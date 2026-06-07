module.exports = {
  content: [
    "./public/**/*.php",
    "./app/**/*.php",
    "./public/assets/js/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Inter", "sans-serif"],
        display: ["Plus Jakarta Sans", "sans-serif"]
      },
      colors: {
        hospital: {
          primary: "#0B5ED7",
          primaryDark: "#084298",
          primaryLight: "#DCEBFF",
          primarySoft: "#EFF6FF",
          navy: "#082032",
          dark: "#061A2D",
          bg: "#F5F9FC",
          card: "#FFFFFF",
          ink: "#0F172A",
          secondary: "#475569",
          muted: "#64748B",
          light: "#94A3B8",
          border: "#E2E8F0",
          borderSoft: "#EEF2F7",
          success: "#16A34A",
          successLight: "#DCFCE7",
          warning: "#F59E0B",
          warningLight: "#FEF3C7",
          danger: "#DC2626",
          dangerLight: "#FEE2E2",
          info: "#0891B2",
          infoLight: "#CFFAFE",
          patients: "#2563EB",
          outpatient: "#10B981",
          inpatient: "#6366F1",
          appointments: "#0EA5E9",
          emergency: "#DC2626",
          laboratory: "#F59E0B",
          radiology: "#06B6D4",
          pharmacy: "#10B981",
          billing: "#14B8A6",
          insurance: "#7C3AED",
          nursing: "#EC4899",
          reports: "#334155",
          queue: "#EA580C",
          settings: "#64748B"
        }
      },
      boxShadow: {
        card: "0 14px 40px rgba(15, 23, 42, 0.08)",
        soft: "0 20px 70px rgba(8, 32, 50, 0.12)"
      },
      borderRadius: {
        sm: "10px",
        md: "14px",
        lg: "18px",
        xl: "24px"
      },
      fontSize: {
        "page-title": ["2rem", { lineHeight: "2.4rem" }],
        "section-title": ["1.375rem", { lineHeight: "1.8rem" }],
        "card-title": ["1rem", { lineHeight: "1.5rem" }],
        "stat": ["2rem", { lineHeight: "2.25rem" }],
        "body": ["1rem", { lineHeight: "1.65rem" }],
        "table": ["0.875rem", { lineHeight: "1.35rem" }],
        "label": ["0.875rem", { lineHeight: "1.25rem" }],
        "helper": ["0.8125rem", { lineHeight: "1.15rem" }]
      }
    }
  },
  plugins: []
};
