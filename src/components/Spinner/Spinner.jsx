import React from "react";
import styles from "./Spinner.module.css";

const Spinner = () => {
    return (
        <div className={styles.spinnerContainer}>
            <div className={styles.spinnerWrapper}>
                <div className={styles.spinner}></div>
                <div className={styles.loader}></div>
            </div>
        </div>
    );
};

export default Spinner;


// import styles from "./Spinner.module.css";
//
// const Spinner = () => {
//     return (
//         <div className={styles.spinnerContainer}>
//             <div className={styles.spinner}></div>
//         </div>
//     );
// };
//
// export default Spinner;














// // import React from 'react';
// import * as React from "react";
//
//
// // import styles from "./Spinner.module.css";
// // import * as styles from "./Spinner.module.css";
// // import styles = require("./Spinner.module.css");
// // import styles from "./Spinner.module.css";
//
//
//
//
//
// import "./Spinner.module.css";
//
//
//
// const Spinner = () => {
//     return (
//         <>
//             <div className="spinnerContainer">
//                 <div className="spinner"></div>
//             </div>
//         </>
//     );
// };
//
// export default Spinner;
