// ColorMods.js
document.addEventListener("DOMContentLoaded", () => {
    const darkBtn = document.getElementById("Dark");
    const lightBtn = document.getElementById("Light");
    let darkMode = false;

    const toggleDarkMode = (enable) => {
        darkMode = enable;

        // ===== Body =====
        document.body.style.backgroundColor = darkMode ? "#121212" : "rgb(255, 248, 248)";
        document.body.style.color = darkMode ? "#e0e0e0" : "#191919";
        document.body.style.marginLeft="5px";

        // ===== Header =====
        const header = document.querySelector("header");
        header.style.backgroundColor = darkMode ? "#1f1f1f" : "rgb(248, 240, 240)";
        header.style.borderBottom = darkMode ? "1px solid #333" : "1px solid rgb(206, 202, 202)";

        // Header title
     document.querySelectorAll(".HeaderTitle").forEach(el => {
    if (el.className === "Register" && el.id==="Register") {
        el.style.color = "#ffffff";
    } else {
        el.style.color = darkMode ? "#e0e0e0" : "#191919";
    }
});
        // Header gradient span
        document.querySelectorAll(".HeaderTitle span  , .Writing h1 span , .ShittyText h1 span , .MiddleTitle h1 span").forEach(el => {
            el.style.background = darkMode 
                ? "linear-gradient(135deg, #ff00ff 0%, #ff1493 50%, #00ffff 100%)"
                : "linear-gradient(135deg, #1e3a8a 0%, #4338ca 50%, #6d28d9 100%)";
            el.style.webkitBackgroundClip = "text";
            el.style.webkitTextFillColor = "transparent";
        });
        // Header links
        document.querySelectorAll("header ul li a").forEach(el => {
            el.style.color = darkMode ? "#e0e0e0" : "#191919";
        });
        // ===== Attractive section =====
        document.querySelectorAll(".Attractive .Writing p, .Attractive .Writing h1").forEach(el => {
            el.style.color = darkMode ? "#e0e0e0" : "#191919";
            
        });
        document.querySelectorAll(".Attractive .PointsWrapper ul li").forEach(li => {
         li.style.color = darkMode ? "#e0e0e0" : "#5d5d5d"; // default light color was gray
        });

    // Also update the span gradient inside h1
        document.querySelectorAll(".Attractive .Writing h1 span .MiddleTitle h1 span").forEach(el => {
        el.style.background = darkMode 
            ? "linear-gradient(135deg, #ff00ff 0%, #ff1493 50%, #00ffff 100%)"
            : "linear-gradient(135deg, #1e3a8a 0%, #4338ca 50%, #6d28d9 100%)";
        el.style.webkitBackgroundClip = "text";
        el.style.webkitTextFillColor = "transparent";
});
        // ABOUT SECTION (I'm crashing out )
        document.querySelectorAll(".About").forEach(el=> {
            el.style.background = darkMode
            ? "#1f1f1f"
            : "rgb(240,248,248)" ;
        });

        // ===== Buttons =====
        document.querySelectorAll(".ButtonStartNow").forEach(btn => {
            if(darkMode){
                btn.style.background = "linear-gradient(135deg, #ff00ff 0%, #ff1493 50%, #00ffff 100%)";
                btn.style.color = "white";
               
            } else {
                btn.style.background = "linear-gradient(135deg, #1e3a8a 0%, #4338ca 50%, #6d28d9 100%)";
                btn.style.color = "white";
                btn.style.boxShadow = "none";
            }
        });
        
          document.querySelectorAll("#Register").forEach(btn => {
            if(darkMode){
                btn.style.background = "linear-gradient(135deg, #ff00ff 0%, #ff1493 50%, #00ffff 100%)";
                btn.style.color = "white";
               
            } else {
                btn.style.background = "rgb(248,240,240)";
                btn.style.color = "black";
                btn.style.boxShadow = "none";
            }
        });
        document.querySelectorAll("#loginForm").forEach(el => {
            if(darkMode){
            el.style.backgroundColor="rgb(20,20,20)";
            }
            else{
                el.style.backgroundColor="rgb(255,248,248)";
            }
        });
        document.querySelectorAll("#loginForm h1").forEach(h1 => {
             if(darkMode){
            h1.style.color="white";
             }
             else{
                h1.style.color="rgb(32, 32, 32)";
             }
        });
        document.querySelectorAll(".Warning h1").forEach(h1 => {
            h1.style.color="#d39e00";
        })
        document.querySelectorAll("#loginForm h3").forEach(h3 => {
            if(darkMode){h3.style.color="white";}
            else {h3.style.color="rgb(32, 32, 32)"}
        });
        document.querySelectorAll(".ReadyToJoin").forEach(el=>{
              if(darkMode){
                el.style.background = "rgb(50,50,50)";
                el.style.boxShadow="transparent";
            } 
            else {
                el.style.background = "rgba(255, 234, 234, 1)";
            }
        })
        document.querySelectorAll("#registerForm").forEach(el => {
            if(darkMode){
            el.style.backgroundColor="rgb(20,20,20)";
            }
            else{
                el.style.backgroundColor="rgb(255,248,248)";
            }
        });
        document.querySelectorAll("#registerForm h3").forEach(h3 => {
            if(darkMode){h3.style.color="white";}
            else {h3.style.color="rgb(32, 32, 32)"}});
          document.querySelectorAll("#registerForm h1").forEach(h1 => {
             if(darkMode){
            h1.style.color="white";
             }
             else{
                h1.style.color="rgb(32, 32, 32)";
             }
        });

        // ===== Feature Blocks =====
        document.querySelectorAll(".FeatureBlock ").forEach(block => {
            block.style.backgroundColor = darkMode ? "#1f1f1f" : "rgb(255,248,248)";
            block.style.color = darkMode ? "#e0e0e0" : "#000";
            block.style.boxShadow = darkMode 
                ? "0 4px 12px rgba(0, 0, 0, 0.3)" 
                : "0 4px 12px rgba(0,0,0,0.1)";
        });
        document.querySelectorAll(".FeatureBlock p").forEach(p => {
            p.style.color= darkMode ? "white" : "black" ;
        });
        document.querySelectorAll(".Features").forEach(el=> {
            el.style.backgroundColor= darkMode ? "rgb(17,17,17)" : "rgb(255,248,248)";
        });
        // ==== MT ====

       const MT = document.querySelectorAll(".MiddleTitle");
        MT.forEach(el => {
         el.style.backgroundColor = darkMode ? "rgb(19,19,19)" : "white";
         el.style.color = darkMode ? "white" : "black"; // optional: change text color too
});
         // ===== Section Texts =====
        document.querySelectorAll(".Paragraphs p, .ShittyText h1, .MiddleTitle h1, .MiddleTitle p, .ReadyToJoin p, .ReadyToJoin h1").forEach(el => {
            el.style.color = darkMode ? "#e0e0e0" : "#191919";
        });

        // ===== Contact Blocks =====
        document.querySelectorAll(".Blocks").forEach(block => {
            block.style.backgroundColor = darkMode ? "#1f1f1f" : "#f5f5f5";
        
        });
        // JoinUs container
    const joinUs = document.querySelector(".JoinUs");
if (joinUs) {
    joinUs.style.backgroundColor = darkMode ? "#1f1f1f" : "#ffffff"; // container background
    joinUs.style.color = darkMode ? "white" : "black"; // default text color
        joinUs.style.boxShadow = darkMode 
        ? "0 4px 20px rgba(255, 105, 180, 0.1)"  // soft pink glow
        : "0 5px 20px rgba(198, 198, 198, 0.5)"; // original light shadow

}
// JoinUs title
const joinTitle = document.querySelector(".ShittyText h1");
if (joinTitle) {
    joinTitle.style.color = darkMode ? "white" : "#191919";
    joinTitle.style.backgroundColor="transparent";
}
  // ===== Light/Dark Button Visibility =====
        lightBtn.style.display = darkMode ? "inline-block" : "none";
        darkBtn.style.display = darkMode ? "none" : "inline-block";
    };
    // Event listeners
    darkBtn.addEventListener("click", () => toggleDarkMode(true));
    lightBtn.addEventListener("click", () => toggleDarkMode(false));
});







