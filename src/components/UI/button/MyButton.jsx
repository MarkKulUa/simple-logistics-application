import React from "react";
import cl from "./MyButton.module.css";

const MyButton = ({
  children,
  additionalClassName,
  ...props
}) => {
  const combinedClasses = `${cl.myBtn} ${additionalClassName}`;

  return (
    <button {...props} className={combinedClasses}>
      {children}
    </button>
  );
};

export default MyButton;
